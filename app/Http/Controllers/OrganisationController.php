<?php

namespace App\Http\Controllers;

use App\Models\Organisation;

class OrganisationController extends Controller
{
    public function index()
    {
        $organisations = Organisation::where('is_approved', true)
            ->where('is_active', true)
            ->with('categories')
            ->orderBy('name')
            ->paginate(12);

        return view('organisations.index', compact('organisations'));
    }

    public function show($id)
    {
        $organisation = Organisation::where('id', $id)
            ->where('is_approved', true)
            ->where('is_active', true)
            ->with(['categories', 'volunteerListings' => function ($q) {
                $q->where('is_active', true)->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        return view('organisations.show', compact('organisation'));
    }
}
