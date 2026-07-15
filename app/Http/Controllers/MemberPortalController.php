<?php

namespace App\Http\Controllers;

class MemberPortalController extends Controller
{
    public function index()
    {
        $member   = auth('member')->user()->load('organisation');
        $benefits = \App\Models\Benefit::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        return view('mein-bereich.index', compact('member', 'benefits'));
    }

    public function benefit($id)
    {
        $benefit = \App\Models\Benefit::where('id', $id)->where('is_active', true)->firstOrFail();
        $member  = auth('member')->user()->load('organisation');
        return view('mein-bereich.benefit', compact('benefit', 'member'));
    }
}
