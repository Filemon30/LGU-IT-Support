<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Issue;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    /**
     * Display services listing.
     */
    public function index()
    {
        $totalCategories = Category::count();
        $totalIssues = Issue::where('status', '!=', 'Soft Delete')->count();

        $categories = Category::with('color')->withCount(['issues' => function ($query) {
            $query->where('status', '!=', 'Soft Delete');
        }])->get();

        $categoryTicketCounts = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->select('categories.category_id', 'categories.category_name', \DB::raw('count(*) as total'))
            ->groupBy('categories.category_id', 'categories.category_name')
            ->pluck('total', 'category_name');

        $categoryData = $categories->map(function ($category) use ($categoryTicketCounts) {
            return [
                'id' => $category->category_id,
                'name' => $category->category_name,
                'description' => $category->description ?? '',
                'icon' => $category->icon ?? 'ti ti-folder',
                'color' => $category->color->color_name ?? 'gray',
                'issues_count' => $category->issues_count,
                'tickets_count' => $categoryTicketCounts[$category->category_name] ?? 0,
            ];
        });

        $colors = Color::orderBy('color_name')->get();

        return $this->ajaxView('admin.services.index', compact(
            'totalCategories',
            'totalIssues',
            'categoryData',
            'colors',
        ));
    }

    /**
     * Display issues for a category.
     */
    public function issues($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $issues = Issue::where('category_id', $categoryId)
            ->where('status', '!=', 'Soft Delete')
            ->with('defaultPriority')
            ->get();

        return $this->ajaxView('admin.services.issues', compact('category', 'issues'));
    }

    /**
     * Display soft deleted issues for a category.
     */
    public function recycleBin($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $issues = Issue::where('category_id', $categoryId)
            ->where('status', 'Soft Delete')
            ->with('defaultPriority')
            ->get();

        return $this->ajaxView('admin.services.recycle_bin', compact('category', 'issues'));
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'description' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|exists:colors,color_name',
        ]);

        $color = Color::where('color_name', $validated['color'])->first();

        $category = Category::create([
            'category_name' => $validated['category_name'],
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'color_id' => $color->color_id,
            'status' => 'Active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully.',
            'category' => $category,
            'color_name' => $color->color_name,
        ]);
    }

    /**
     * Store a new issue.
     */
    public function storeIssue(Request $request, $categoryId)
    {
        $validated = $request->validate([
            'description' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('issues')->where(function ($query) use ($categoryId) {
                    return $query->where('category_id', $categoryId);
                }),
            ],
            'default_priority_level_id' => 'required|exists:priority_levels,priority_level_id',
        ]);

        $issue = Issue::create([
            'category_id' => $categoryId,
            'issue_ref_num' => 'ISS-' . strtoupper(uniqid()),
            'description' => $validated['description'],
            'default_priority_level_id' => $validated['default_priority_level_id'],
            'status' => 'Active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Issue added successfully.',
            'issue' => $issue,
        ]);
    }

    /**
     * Soft delete an issue.
     */
    public function deleteIssue($categoryId, $issueId)
    {
        $issue = Issue::where('issue_id', $issueId)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        $issue->update(['status' => 'Soft Delete']);

        return response()->json([
            'success' => true,
            'message' => 'Issue deleted successfully.',
        ]);
    }

    /**
     * Restore a soft deleted issue.
     */
    public function restoreIssue($categoryId, $issueId)
    {
        $issue = Issue::where('issue_id', $issueId)
            ->where('category_id', $categoryId)
            ->where('status', 'Soft Delete')
            ->firstOrFail();

        $issue->update(['status' => 'Active']);

        return response()->json([
            'success' => true,
            'message' => 'Issue restored successfully.',
        ]);
    }

    /**
     * Render a view, returning only the content section
     * for AJAX requests.
     */
    protected function ajaxView(string $view, array $data = [])
    {
        if (
            request()->ajax() ||
            request()->header('X-Requested-With') === 'XMLHttpRequest'
        ) {

            return response(
                view($view, $data)
                    ->renderSections()['content'] ?? ''
            );
        }

        return view($view, $data);
    }
}
