<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\ItemRepositoryInterface;
use Illuminate\Http\Request;

class ItemDbController extends Controller
{
    public function __construct(
        protected ItemRepositoryInterface $items
    ) {}

    public function index(Request $request)
    {
        $keyword = trim($request->query('q', ''));
        $type = $request->query('type');
        $page = (int) $request->query('page', 1);

        $results = null;
        if (!empty($keyword)) {
            $results = $this->items->search($keyword, $type, 20, $page);
        }

        $itemTypes = $this->items->getItemTypes();

        return view('itemdb.index', compact('results', 'keyword', 'type', 'itemTypes'));
    }

    public function show(int $id)
    {
        $item = $this->items->findById($id);
        if (!$item) {
            abort(404, 'Item not found in emulator database.');
        }

        return view('itemdb.show', compact('item'));
    }
}

