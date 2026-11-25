<?php

namespace App\Services\CashShop;

use App\Models\CashShopProduct;
use App\Models\CashShopPurchase;
use App\Models\User;
use App\Services\CashPoints\CashPointsService;
use App\Services\Escrow\EscrowInboxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashShopPurchaseService
{
    public function __construct(
        protected CashPointsService $cashPointsService,
        protected EscrowInboxService $escrowInboxService
    ) {}

    /**
     * Process a Cash Shop purchase atomically with Escrow delivery.
     *
     * @throws \RuntimeException
     */
    public function purchase(User $user, int $productId, int $quantity = 1, ?string $ipAddress = null): CashShopPurchase
    {
        if ($quantity < 1 || $quantity > 100) {
            throw new \RuntimeException('Invalid purchase quantity.');
        }

        if (!$user->account_id) {
            throw new \RuntimeException('Your web account is not linked to an in-game Ragnarok account.');
        }

        return DB::transaction(function () use ($user, $productId, $quantity, $ipAddress) {
            // 1. Lock and validate product
            $product = CashShopProduct::where('id', $productId)->lockForUpdate()->first();
            if (!$product) {
                throw new \RuntimeException('Product not found.');
            }

            $currentBalance = $this->cashPointsService->getBalance($user->account_id);

            $eligibility = $product->canPurchase($user->account_id, $quantity, $currentBalance);
            if (!$eligibility['allowed']) {
                throw new \RuntimeException($eligibility['reason'] ?? 'Purchase not permitted.');
            }

            $unitPrice = $product->effective_price;
            $totalCost = $unitPrice * $quantity;

            // 2. Generate unique purchase code
            $purchaseCode = 'CS-' . strtoupper(Str::random(10));

            // 3. Atomically deduct Cash Points from emulator acc_reg_num
            $this->cashPointsService->deduct(
                $user->account_id,
                $totalCost,
                "Cash Shop purchase: {$product->name} ({$quantity}x)",
                $purchaseCode
            );

            // 4. Decrement product stock if limited
            if (!$product->unlimited_stock) {
                $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
                $product->save();
            }

            // 5. Deposit purchased item into Escrow Inbox
            $meta = $product->item_metadata ?? [];
            $totalDeliveredQty = $product->item_quantity * $quantity;

            $escrowItem = $this->escrowInboxService->depositItem(
                $user->account_id,
                [
                    'user_id' => $user->id,
                    'source_type' => 'cash_shop',
                    'source_id' => $product->id,
                    'reward_type' => 'item',
                    'item_id' => $product->item_id,
                    'item_name' => $product->name,
                    'quantity' => $totalDeliveredQty,
                    'refine' => (int) ($meta['refine'] ?? 0),
                    'card0' => (int) ($meta['card0'] ?? 0),
                    'card1' => (int) ($meta['card1'] ?? 0),
                    'card2' => (int) ($meta['card2'] ?? 0),
                    'card3' => (int) ($meta['card3'] ?? 0),
                    'bound' => (int) ($meta['bound'] ?? 0),
                    'identified' => true,
                    'title' => "Cash Shop: {$product->name}",
                    'message' => "Purchased from Web Item Mall ({$quantity}x purchase, {$totalDeliveredQty}x items). Ready for in-game transfer.",
                ]
            );

            // 6. Record financial purchase transaction
            return CashShopPurchase::create([
                'purchase_code' => $purchaseCode,
                'user_id' => $user->id,
                'account_id' => $user->account_id,
                'product_id' => $product->id,
                'product_name_snapshot' => $product->name,
                'item_id' => $product->item_id,
                'item_quantity' => $totalDeliveredQty,
                'cash_points_spent' => $totalCost,
                'escrow_item_id' => $escrowItem->id,
                'status' => 'completed',
                'ip_address' => $ipAddress,
                'metadata' => [
                    'unit_price' => $unitPrice,
                    'is_on_sale' => $product->is_on_sale,
                    'regular_price' => $product->cash_points_price,
                    'purchase_quantity' => $quantity,
                    'item_metadata' => $meta,
                ],
            ]);
        });
    }
}
