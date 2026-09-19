<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $pages = $query
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.index', [
            'pages' => $pages,
            'active' => 'pages',
        ]);
    }

    public function create()
    {
        return view('admin.pages.create', [
            'active' => 'pages',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $validated['is_active'] = $request->boolean('is_active');

        DB::beginTransaction();
        try {
            Page::create($validated);
            DB::commit();

            return redirect()
                ->route('admin.pages.index')
                ->with('success', 'Page created successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withInput()->with('error', 'Unable to create page.');
        }
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', [
            'page' => $page,
            'active' => 'pages',
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $this->validateRequest($request, $page->id);
        $validated['is_active'] = $request->boolean('is_active');

        DB::beginTransaction();
        try {
            $page->update($validated);
            DB::commit();

            return redirect()
                ->route('admin.pages.index')
                ->with('success', 'Page updated successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withInput()->with('error', 'Unable to update page.');
        }
    }

    public function destroy(Page $page)
    {
        try {
            $page->delete();

            return back()->with('success', 'Page deleted successfully.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Unable to delete page.');
        }
    }

    protected function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        $slug = $request->input('slug');

        if (blank($slug) && filled($request->input('title'))) {
            $slug = Str::slug($request->input('title'));
        }

        $request->merge(['slug' => $slug]);

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('pages', 'slug')->ignore($ignoreId),
            ],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
