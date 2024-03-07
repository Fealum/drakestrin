<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EncyclopediaController extends Controller
{
    public function index()
    {
        $pages = Page::where('encyclopedia', 0)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
        return view('encyclopedia.index', ['pages' => $pages]);
    }

    public function view(Page $page)
    {
        return view('encyclopedia.page', ['page' => $page]);
    }

    public function create(Request $request)
    {
        if ($this->permissionService->check('createEncyclopedia') === 0) {
            abort(403);
        }

        if ($request->isMethod('post')) {
        }

        return view('encyclopedia.create');
    }

    public function edit()
    {
        return view('encyclopedia.edit');
    }

    public function delete()
    {
        return view('encyclopedia.delete');
    }
}
