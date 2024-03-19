<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EncyclopediaController extends Controller
{
    public function index()
    {
        $pages = Page::where('page_id', 0)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
        return view('encyclopedia.index', ['pages' => $pages]);
    }

    public function view(Page $page)
    {
        if ($this->permissionService->check('show', $page) === 0) {
            abort(403);
        }

        $this->setLocation($page);

        return view('encyclopedia.page', ['page' => $page]);
    }

    public function create(Page $page, Request $request)
    {
        if ($this->permissionService->check('createEncyclopedia') === 0) {
            abort(403);
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required',
                'title' => 'required',
                'text' => 'required',
                'sort' => 'numeric',
            ]);

            $newPage = new Page;

            $newPage->user_id = auth()->id();
            $newPage->name = trim($validated['name']);
            $newPage->title = trim($validated['title']);
            $newPage->text = trim($validated['text']);
            $newPage->sort = $validated['sort'] ?? 0;
            $newPage->activated = 1;
            $newPage->page_id = $page->id;

            $newPage->save();

            return to_route('encyclopedia.view', ['page' => $newPage->id]);
        }

        return view('encyclopedia.create', ['page' => $page]);
    }

    public function edit(Page $page, Request $request)
    {
        if ($this->permissionService->check('editEncyclopedia', $page) === 0 || $this->permissionService->check('editEncyclopedia', $page) === 1 && $page->user->id !== auth()->id()) {
            abort(403);
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required',
                'title' => 'required',
                'text' => 'required',
                'parent' => 'required',
                'sort' => 'numeric',
            ]);

            $page->name = trim($validated['name']);
            $page->title = trim($validated['title']);
            $page->text = trim($validated['text']);
            $page->sort = $validated['sort'] ?? 0;
            $page->page_id = $validated['parent'] ?? 0;

            $page->save();

            return to_route('encyclopedia.view', ['page' => $page->id]);
        }

        $allPages = Page::where('page_id', 0)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        return view('encyclopedia.edit', ['page' => $page, 'allPages' => $allPages]);
    }

    public function delete(Page $page, Request $request)
    {
        if ($this->permissionService->check('deleteEncyclopedia', $page) === 0 || $this->permissionService->check('deleteEncyclopedia', $page) === 1 && $page->user->id !== auth()->id()) {
            abort(403);
        }

        if ($request->isMethod('post')) {
            $page->delete();
            return to_route('encyclopedia');
        }

        return view('encyclopedia.delete', ['page' => $page]);
    }
}
