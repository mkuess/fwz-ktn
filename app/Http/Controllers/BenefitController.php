<?php

namespace App\Http\Controllers;

use App\Models\Benefit;

class BenefitController extends Controller
{
    public function index()
    {
        $benefits = Benefit::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('benefits.index', compact('benefits'));
    }
}
