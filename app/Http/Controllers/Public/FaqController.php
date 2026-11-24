<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Faq\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(
        protected FaqService $faqService
    ) {}

    /**
     * Public FAQ & Knowledge Base Landing Page
     */
    public function index(Request $request): View
    {
        // Seed default Ragnarok Online FAQs if table is brand new
        $this->faqService->seedDefaultsIfEmpty();

        $search = $request->query('q');
        $data = $this->faqService->getPublicFaqHomeData($search);

        return view('faq.index', $data);
    }

    /**
     * Browse FAQs by Category
     */
    public function category(string $slug): View
    {
        $data = $this->faqService->getCategoryFaqs($slug);
        return view('faq.category', $data);
    }

    /**
     * Single FAQ Question & Full Answer Detail
     */
    public function show(string $slug): View
    {
        $accountId = auth()->user()?->account_id ?? auth()->id();
        $ip = request()->ip();

        $faq = $this->faqService->getFaqBySlug($slug, $accountId, $ip);
        $hasVoted = $this->faqService->hasVoted($faq->id, $accountId, $ip);

        return view('faq.show', compact('faq', 'hasVoted'));
    }

    /**
     * Submit Helpful / Not Helpful Feedback
     */
    public function feedback(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:helpful,not_helpful'],
        ]);

        $accountId = auth()->user()?->account_id ?? auth()->id();
        $ip = $request->ip();

        $result = $this->faqService->submitFeedback(
            $id,
            $request->input('type'),
            $accountId,
            $ip
        );

        return response()->json($result);
    }
}
