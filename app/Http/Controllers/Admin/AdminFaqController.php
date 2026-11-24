<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Services\Faq\FaqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFaqController extends Controller
{
    public function __construct(
        protected FaqService $faqService
    ) {}

    /**
     * FAQ Dashboard & Table Management
     */
    public function index(Request $request): View
    {
        $this->faqService->seedDefaultsIfEmpty();

        $stats = $this->faqService->getAdminDashboardStats();
        $faqs = $this->faqService->getAdminFaqs($request->all(), 20);
        $categories = FaqCategory::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.faq.index', compact('stats', 'faqs', 'categories'));
    }

    /**
     * Create New FAQ Form
     */
    public function create(): View
    {
        $categories = FaqCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $otherFaqs = Faq::select('id', 'question')->orderBy('question')->get();

        return view('admin.faq.create', compact('categories', 'otherFaqs'));
    }

    /**
     * Store New FAQ
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:faq_categories,id'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:faqs,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'related_ids' => ['nullable', 'array'],
            'related_ids.*' => ['exists:faqs,id'],
        ]);

        $this->faqService->createFaq($validated, auth()->id());

        return redirect()->route('admin.faq.index')->with('success', 'FAQ question created successfully.');
    }

    /**
     * Edit FAQ Form
     */
    public function edit(int $id): View
    {
        $faq = Faq::with('relatedQuestions')->findOrFail($id);
        $categories = FaqCategory::orderBy('sort_order')->orderBy('name')->get();
        $otherFaqs = Faq::where('id', '!=', $id)->select('id', 'question')->orderBy('question')->get();

        return view('admin.faq.edit', compact('faq', 'categories', 'otherFaqs'));
    }

    /**
     * Update FAQ
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:faq_categories,id'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:faqs,slug,' . $faq->id],
            'short_description' => ['nullable', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'related_ids' => ['nullable', 'array'],
            'related_ids.*' => ['exists:faqs,id'],
        ]);

        $this->faqService->updateFaq($faq, $validated, auth()->id());

        return redirect()->route('admin.faq.index')->with('success', 'FAQ updated successfully.');
    }

    /**
     * Delete FAQ
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->faqService->deleteFaq($id);
        return redirect()->route('admin.faq.index')->with('success', 'FAQ deleted successfully.');
    }

    /**
     * Category Management View
     */
    public function categories(): View
    {
        $categories = FaqCategory::withCount('faqs')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.faq.categories', compact('categories'));
    }

    /**
     * Store New Category
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:faq_categories,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->faqService->createCategory($validated);

        return back()->with('success', 'FAQ category added successfully.');
    }

    /**
     * Update Category
     */
    public function updateCategory(Request $request, int $id): RedirectResponse
    {
        $category = FaqCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:faq_categories,slug,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->faqService->updateCategory($category, $validated);

        return back()->with('success', 'FAQ category updated successfully.');
    }

    /**
     * Delete Category
     */
    public function destroyCategory(Request $request, int $id): RedirectResponse
    {
        $reassignTo = $request->input('reassign_to_id') ? (int) $request->input('reassign_to_id') : null;
        $result = $this->faqService->deleteCategory($id, $reassignTo);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Toggle FAQ Published Status
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);
        $faq->status = ($faq->status === 'published') ? 'draft' : 'published';
        $faq->save();

        ActivityLog::record('faq.status', "Toggled FAQ #{$faq->id} status to {$faq->status}");

        return back()->with('success', "FAQ '{$faq->question}' status changed to " . ucfirst($faq->status) . '.');
    }
}
