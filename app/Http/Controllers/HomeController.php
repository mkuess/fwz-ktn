<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Benefit;
use App\Models\Category;
use App\Models\Organisation;
use App\Models\Testimonial;
use App\Models\VolunteerListing;

class HomeController extends Controller
{
    public function index()
    {
        $vereine = Organisation::query()
            ->where('is_approved', true)
            ->where('is_active', true)
            ->with('categories')
            ->orderBy('name')
            ->take(8)
            ->get()
            ->map(fn (Organisation $organisation): array => [
                'id' => $organisation->id,
                'name' => $organisation->name,
                'ort' => trim(($organisation->zip ?? '').' '.($organisation->city ?? '')),
                'logo_url' => $organisation->logo_path ? asset('storage/'.$organisation->logo_path) : null,
                'categories' => $organisation->categories->pluck('name')->values()->all(),
            ]);

        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $aktionen = Article::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $volunteerListings = VolunteerListing::query()
            ->publiclyVisible()
            ->with(['organisation', 'categories', 'activities'])
            ->latest()
            ->limit(3)
            ->get();

        $featuredBenefits = Benefit::where('is_active', true)
            ->orderByDesc('is_teaser')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $stats = [
            'vereine' => Organisation::where('is_approved', true)->count().'+',
            'freiwillige' => '200.000',
            'stunden' => '15.000',
        ];

        return view('home', compact('vereine', 'categories', 'aktionen', 'volunteerListings', 'featuredBenefits', 'testimonials', 'stats'));
    }

    public function impressum()
    {
        return view('impressum');
    }

    public function datenschutz()
    {
        return view('datenschutz');
    }

    public function barrierefreiheit()
    {
        return view('barrierefreiheit');
    }

    public function inArbeit()
    {
        return view('in-arbeit');
    }
}
