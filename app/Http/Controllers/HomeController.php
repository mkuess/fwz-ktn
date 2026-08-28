<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Organisation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $vereine = Organisation::query()
            ->where('is_approved', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->take(8)
            ->get()
            ->map(fn ($org) => [
                'id'       => $org->id,
                'kuerzel'  => $this->abbreviation($org->name),
                'name'     => $org->name,
                'ort'      => trim(($org->zip ?? '') . ' ' . ($org->city ?? '')),
                'logo_url' => $org->logo_path ? asset('storage/' . $org->logo_path) : null,
            ]);

        $kategorien = Category::orderBy('sort_order')->get(['name', 'slug']);

        $aktionen = \App\Models\Article::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $featuredBenefits = \App\Models\Benefit::where('is_active', true)
            ->orderByDesc('is_teaser')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $testimonials = \App\Models\Testimonial::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $stats = [
            'vereine'          => Organisation::where('is_approved', true)->count() . '+',
            'freiwillige'      => '200.000',
            'stunden'          => '15.000',
            'engagementFelder' => Category::count(),
        ];

        return view('home', compact('vereine', 'kategorien', 'aktionen', 'featuredBenefits', 'testimonials', 'stats'));
    }

    public function vereineSuche(Request $request)
    {
        $q         = trim($request->get('q', ''));
        $kategorie = trim($request->get('kategorie', ''));

        $query = Organisation::query()
            ->where('is_approved', true)
            ->where('is_active', true);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhere('city', 'like', '%' . $q . '%')
                    ->orWhere('zip',  'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }

        if ($kategorie !== '' && $kategorie !== 'alle') {
            $query->whereHas('categories', fn ($sub) => $sub->where('slug', $kategorie));
        }

        $total   = (clone $query)->count();
        $results = $query->orderBy('name')->take(8)->get()
            ->map(fn ($org) => [
                'name'     => $org->name,
                'ort'      => trim(($org->zip ?? '') . ' ' . ($org->city ?? '')),
                'kuerzel'  => $this->abbreviation($org->name),
                'logo_url' => $org->logo_path ? asset('storage/' . $org->logo_path) : null,
            ]);

        return response()->json(['total' => $total, 'results' => $results]);
    }

    private function abbreviation(string $name): string
    {
        $abbr = collect(explode(' ', $name))
            ->filter(fn ($w) => mb_strlen($w) > 2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(4)
            ->implode('');

        return $abbr ?: mb_strtoupper(mb_substr($name, 0, 3));
    }

    public function impressum()       { return view('impressum'); }
    public function datenschutz()     { return view('datenschutz'); }
    public function barrierefreiheit(){ return view('barrierefreiheit'); }
    public function inArbeit()        { return view('in-arbeit'); }
}
