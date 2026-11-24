<?php

namespace App\Services\Faq;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\FaqFeedback;
use App\Models\FaqSearchLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class FaqService
{
    /**
     * Get data for public FAQ landing page
     */
    public function getPublicFaqHomeData(?string $search = null): array
    {
        $categories = FaqCategory::where('is_active', true)
            ->withCount(['publishedFaqs as published_faqs_count'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $featuredFaqs = Faq::published()
            ->with('category')
            ->featured()
            ->orderBy('views_count', 'desc')
            ->take(6)
            ->get();

        $recentFaqs = Faq::published()
            ->with('category')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $searchResults = null;
        if (!empty($search)) {
            $term = trim($search);
            $searchResults = Faq::published()
                ->with('category')
                ->search($term)
                ->orderBy('views_count', 'desc')
                ->paginate(12)
                ->withQueryString();

            // Log search query for analytics
            try {
                FaqSearchLog::create([
                    'search_term' => Str::limit($term, 100),
                    'results_count' => $searchResults->total(),
                    'ip_address' => request()->ip(),
                ]);
            } catch (\Throwable) {
                // Ignore log errors
            }
        }

        return [
            'categories' => $categories,
            'featuredFaqs' => $featuredFaqs,
            'recentFaqs' => $recentFaqs,
            'searchResults' => $searchResults,
            'searchTerm' => $search,
        ];
    }

    /**
     * Get FAQs by Category Slug
     */
    public function getCategoryFaqs(string $slug, int $perPage = 15): array
    {
        $category = FaqCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $faqs = Faq::published()
            ->where('category_id', $category->id)
            ->orderBy('is_featured', 'desc')
            ->orderBy('views_count', 'desc')
            ->paginate($perPage);

        return [
            'category' => $category,
            'faqs' => $faqs,
        ];
    }

    /**
     * Find single FAQ by slug and track view
     */
    public function getFaqBySlug(string $slug, ?int $accountId = null, ?string $ip = null): Faq
    {
        $faq = Faq::published()
            ->where('slug', $slug)
            ->with([
                'category',
                'relatedQuestions' => function ($q) {
                    $q->published();
                }
            ])
            ->firstOrFail();

        // Increment views
        $faq->increment('views_count');

        return $faq;
    }

    /**
     * Check if user/IP already voted on this FAQ
     */
    public function hasVoted(int $faqId, ?int $accountId = null, ?string $ip = null): bool
    {
        $query = FaqFeedback::where('faq_id', $faqId);

        if ($accountId) {
            return $query->where('account_id', $accountId)->exists();
        }

        if ($ip) {
            return $query->where('ip_address', $ip)
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();
        }

        return false;
    }

    /**
     * Record player feedback
     */
    public function submitFeedback(int $faqId, string $type, ?int $accountId = null, ?string $ip = null): array
    {
        if (!in_array($type, ['helpful', 'not_helpful'])) {
            return ['success' => false, 'message' => 'Invalid feedback type.'];
        }

        $faq = Faq::published()->findOrFail($faqId);

        if ($this->hasVoted($faqId, $accountId, $ip)) {
            return ['success' => false, 'message' => 'You have already submitted feedback for this answer.'];
        }

        FaqFeedback::create([
            'faq_id' => $faq->id,
            'account_id' => $accountId,
            'feedback_type' => $type,
            'ip_address' => $ip,
        ]);

        return [
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'helpful_percentage' => $faq->helpfulPercentage(),
        ];
    }

    /**
     * Get statistics for admin dashboard
     */
    public function getAdminDashboardStats(): array
    {
        $totalFaqs = Faq::count();
        $published = Faq::where('status', 'published')->count();
        $draft = Faq::where('status', 'draft')->count();
        $archived = Faq::where('status', 'archived')->count();
        $categoriesCount = FaqCategory::count();
        $totalViews = Faq::sum('views_count');
        $totalFeedback = FaqFeedback::count();
        $helpfulCount = FaqFeedback::where('feedback_type', 'helpful')->count();
        $notHelpfulCount = FaqFeedback::where('feedback_type', 'not_helpful')->count();

        $overallHelpfulPercent = $totalFeedback > 0 ? (int) round(($helpfulCount / $totalFeedback) * 100) : 100;

        // Top searched keywords without results
        $unansweredSearches = FaqSearchLog::where('results_count', 0)
            ->selectRaw('search_term, count(*) as search_count')
            ->groupBy('search_term')
            ->orderBy('search_count', 'desc')
            ->take(5)
            ->get();

        return [
            'totalFaqs' => $totalFaqs,
            'published' => $published,
            'draft' => $draft,
            'archived' => $archived,
            'categoriesCount' => $categoriesCount,
            'totalViews' => $totalViews,
            'helpfulPercentage' => $overallHelpfulPercent,
            'helpfulCount' => $helpfulCount,
            'notHelpfulCount' => $notHelpfulCount,
            'unansweredSearches' => $unansweredSearches,
        ];
    }

    /**
     * Get paginated FAQs for admin panel
     */
    public function getAdminFaqs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Faq::with('category')->withCount([
            'feedback as helpful_count' => function ($q) {
                $q->where('feedback_type', 'helpful');
            },
            'feedback as total_feedback_count'
        ]);

        if (!empty($filters['search'])) {
            $query->search(trim($filters['search']));
        }

        if (!empty($filters['category_id']) && $filters['category_id'] !== 'all') {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['featured']) && $filters['featured'] !== 'all' && $filters['featured'] !== '') {
            $query->where('is_featured', (bool) $filters['featured']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Create FAQ
     */
    public function createFaq(array $data, int $userId): Faq
    {
        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['question']);
        $uniqueSlug = $slug;
        $c = 1;
        while (Faq::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $c++;
        }

        $faq = Faq::create([
            'category_id' => $data['category_id'] ?? null,
            'question' => $data['question'],
            'slug' => $uniqueSlug,
            'short_description' => $data['short_description'] ?? null,
            'answer' => $data['answer'],
            'keywords' => $data['keywords'] ?? null,
            'status' => $data['status'] ?? 'published',
            'is_featured' => !empty($data['is_featured']),
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'published_at' => !empty($data['published_at']) ? $data['published_at'] : now(),
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        if (!empty($data['related_ids']) && is_array($data['related_ids'])) {
            $faq->relatedQuestions()->sync($data['related_ids']);
        }

        return $faq;
    }

    /**
     * Update FAQ
     */
    public function updateFaq(Faq $faq, array $data, int $userId): Faq
    {
        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['question']);
        $uniqueSlug = $slug;
        $c = 1;
        while (Faq::where('slug', $uniqueSlug)->where('id', '!=', $faq->id)->exists()) {
            $uniqueSlug = $slug . '-' . $c++;
        }

        $faq->update([
            'category_id' => $data['category_id'] ?? null,
            'question' => $data['question'],
            'slug' => $uniqueSlug,
            'short_description' => $data['short_description'] ?? null,
            'answer' => $data['answer'],
            'keywords' => $data['keywords'] ?? null,
            'status' => $data['status'] ?? 'published',
            'is_featured' => !empty($data['is_featured']),
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'published_at' => !empty($data['published_at']) ? $data['published_at'] : $faq->published_at,
            'updated_by' => $userId,
        ]);

        if (isset($data['related_ids']) && is_array($data['related_ids'])) {
            $faq->relatedQuestions()->sync($data['related_ids']);
        }

        return $faq;
    }

    /**
     * Delete FAQ
     */
    public function deleteFaq(int $id): bool
    {
        $faq = Faq::findOrFail($id);
        return $faq->delete();
    }

    /**
     * Create Category
     */
    public function createCategory(array $data): FaqCategory
    {
        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $uniqueSlug = $slug;
        $c = 1;
        while (FaqCategory::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $c++;
        }

        return FaqCategory::create([
            'name' => $data['name'],
            'slug' => $uniqueSlug,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? 'help-circle',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => !empty($data['is_active']),
        ]);
    }

    /**
     * Update Category
     */
    public function updateCategory(FaqCategory $category, array $data): FaqCategory
    {
        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $uniqueSlug = $slug;
        $c = 1;
        while (FaqCategory::where('slug', $uniqueSlug)->where('id', '!=', $category->id)->exists()) {
            $uniqueSlug = $slug . '-' . $c++;
        }

        $category->update([
            'name' => $data['name'],
            'slug' => $uniqueSlug,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? 'help-circle',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => !empty($data['is_active']),
        ]);

        return $category;
    }

    /**
     * Delete Category with reassignment check
     */
    public function deleteCategory(int $id, ?int $reassignToId = null): array
    {
        $category = FaqCategory::withCount('faqs')->findOrFail($id);

        if ($category->faqs_count > 0) {
            if ($reassignToId && $reassignToId !== $category->id) {
                Faq::where('category_id', $category->id)->update(['category_id' => $reassignToId]);
            } else {
                return [
                    'success' => false,
                    'message' => "Cannot delete category '{$category->name}' because it contains {$category->faqs_count} questions. Please reassign or delete them first."
                ];
            }
        }

        $category->delete();

        return [
            'success' => true,
            'message' => "Category '{$category->name}' deleted successfully."
        ];
    }

    /**
     * Seed initial Ragnarok Online FAQ data if database is empty
     */
    public function seedDefaultsIfEmpty(): void
    {
        if (FaqCategory::count() > 0) {
            return;
        }

        $categories = [
            [
                'name' => 'Account & Registration',
                'slug' => 'account-registration',
                'icon' => 'user-check',
                'description' => 'Managing game master credentials, multi-accounts, password reset, and 2FA protection.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Downloads & Installation',
                'slug' => 'downloads-installation',
                'icon' => 'download-cloud',
                'description' => 'Client installers, gepard shield compatibility, direct mirrors, and patcher issues.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Gameplay & Mechanics',
                'slug' => 'gameplay-mechanics',
                'icon' => 'swords',
                'description' => 'Server rates, drop mechanics, War of Emperium schedules, and quest guides.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Donations & Cash Points',
                'slug' => 'donations-cash-points',
                'icon' => 'gem',
                'description' => 'Payment methods, Cash Point conversions, VIP privileges, and reward delivery.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'icon' => 'terminal',
                'description' => 'DirectX graphics errors, dinput.dll crashes, sound setup, and high-DPI scaling.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Rules & Regulations',
                'slug' => 'rules-regulations',
                'icon' => 'shield-alert',
                'description' => 'Server conduct policies, third-party macro bans, market scam rules, and penalty guidelines.',
                'sort_order' => 6,
            ],
        ];

        $catMap = [];
        foreach ($categories as $c) {
            $cat = FaqCategory::create(array_merge($c, ['is_active' => true]));
            $catMap[$c['slug']] = $cat->id;
        }

        $faqs = [
            [
                'category_slug' => 'account-registration',
                'question' => 'Can I register more than one master account?',
                'slug' => 'can-i-register-multiple-accounts',
                'short_description' => 'Our multi-account policy and dual-client allowance.',
                'answer' => "<p>Yes, players are allowed to register up to <strong>3 game accounts</strong> per household. Dual-clienting is fully supported for venders and party buffs, but unattended third-party automated botting is strictly prohibited.</p><p>You can manage all of your characters directly through the <em>AetherCP Player Dashboard</em>.</p>",
                'keywords' => 'multi account, dual client, registration, accounts',
                'is_featured' => true,
                'views_count' => 142,
            ],
            [
                'category_slug' => 'downloads-installation',
                'question' => 'How do I install the full game client and patcher?',
                'slug' => 'how-to-install-game-client',
                'short_description' => 'Step-by-step instructions for installing the client and running the auto-patcher.',
                'answer' => "<p>Follow these 3 easy steps to begin your journey:</p><ol><li>Download our Full Client installer or ZIP package from the <a href='/downloads'>Downloads Page</a>.</li><li>Extract or install into a clean folder (e.g. <code>C:\Games\AetherRO</code>). Avoid installing inside <code>Program Files</code> to prevent Windows UAC write permissions errors.</li><li>Run <code>AetherPatcher.exe</code> as Administrator to receive the latest sprites, maps, and item tables.</li></ol>",
                'keywords' => 'install, client, download, patcher, setup',
                'is_featured' => true,
                'views_count' => 310,
            ],
            [
                'category_slug' => 'gameplay-mechanics',
                'question' => 'What are the server base rates and drop mechanics?',
                'slug' => 'server-rates-and-drop-mechanics',
                'short_description' => 'Comprehensive breakdown of base experience, job experience, and drop rates.',
                'answer' => "<p>Our server operates on tailored custom rates designed for balanced MMORPG progression:</p><ul><li><strong>Base Experience:</strong> 25x</li><li><strong>Job Experience:</strong> 25x</li><li><strong>Common Monster Drops:</strong> 15x</li><li><strong>Equipment Drops:</strong> 15x</li><li><strong>Normal Monster Cards:</strong> 0.20% (20x)</li><li><strong>MVP Boss Equipment:</strong> 5x</li><li><strong>MVP Boss Cards:</strong> 0.01% (Official Rate)</li></ul><p>Use <code>@rates</code> in-game anytime to check active server event multipliers.</p>",
                'keywords' => 'rates, exp, drop rates, card drop, mvp',
                'is_featured' => true,
                'views_count' => 520,
            ],
            [
                'category_slug' => 'donations-cash-points',
                'question' => 'How long does it take for Cash Points to arrive after donating?',
                'slug' => 'how-long-for-cash-points-delivery',
                'short_description' => 'Automated payment processing times and verification steps.',
                'answer' => "<p>Automated payment methods (PayPal and Stripe) credit your Cash Points <strong>instantly within 60 seconds</strong>. For manual remittances or bank transfers, please allow 1 to 12 hours for administrator verification.</p><p>You can review your credit ledger anytime under your Account Profile.</p>",
                'keywords' => 'cash points, donation, paypal, instant delivery, cp',
                'is_featured' => true,
                'views_count' => 95,
            ],
            [
                'category_slug' => 'technical-support',
                'question' => 'How do I fix the "Cannot init d3d OR grf file has not found" crash?',
                'slug' => 'fix-cannot-init-d3d-error',
                'short_description' => 'Resolving resolution and DirectX compatibility errors on Windows 10 & 11.',
                'answer' => "<p>This is a standard DirectX display adapter handshake error. To solve it:</p><ol><li>Open your game folder and right-click <code>Setup.exe</code> &rarr; <strong>Run as Administrator</strong>.</li><li>In the Graphics Device dropdown, choose your dedicated graphics card (NVIDIA / AMD / Direct3D HAL) rather than default software rendering.</li><li>Select a resolution matching your monitor aspect ratio (e.g. 1920x1080x32).</li><li>Click OK and launch the game through the patcher.</li></ol>",
                'keywords' => 'd3d, crash, setup, error, resolution, directx',
                'is_featured' => true,
                'views_count' => 418,
            ],
            [
                'category_slug' => 'rules-regulations',
                'question' => 'What is the server policy on macro software and automation?',
                'slug' => 'macro-and-automation-policy',
                'short_description' => 'Guidelines regarding auto-hotkey scripts, mouse macros, and botting.',
                'answer' => "<p>Keyboard repeat keys (such as hardware turbo keys or spamming a skill key while holding it down) are allowed for quality of life. However, <strong>unattended automated botting</strong>, conditional scripts (pixel reading, packet botting, memory injectors), and automated farming while AFK result in an immediate permanent ban on all associated master accounts.</p>",
                'keywords' => 'rules, macro, bot, ahk, ban policy',
                'is_featured' => false,
                'views_count' => 88,
            ],
        ];

        foreach ($faqs as $f) {
            $catId = $catMap[$f['category_slug']] ?? null;
            unset($f['category_slug']);
            Faq::create(array_merge($f, [
                'category_id' => $catId,
                'status' => 'published',
                'published_at' => now(),
            ]));
        }
    }
}
