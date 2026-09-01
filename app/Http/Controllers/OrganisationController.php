<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganisationController extends Controller
{
    public function index(Request $request)
    {
        app()->setLocale('de');

        $organisations = $this->filteredQuery($request)
            ->with('categories')
            ->orderBy('name')
            ->paginate(12);
        $categories = $this->categoryOptions();

        return view('organisations.index', compact('organisations', 'categories'));
    }

    public function search(Request $request)
    {
        $perPage = min(max((int) $request->input('limit', 12), 1), 24);
        $page = max((int) $request->input('page', 1), 1);
        $organisations = $this->filteredQuery($request)
            ->with('categories')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'total' => $organisations->total(),
            'page' => $organisations->currentPage(),
            'last_page' => $organisations->lastPage(),
            'has_more' => $organisations->hasMorePages(),
            'results' => $organisations->getCollection()
                ->map(fn (Organisation $organisation): array => $this->searchResult($organisation))
                ->values(),
        ]);
    }

    public function map()
    {
        $organisations = Organisation::query()
            ->where('is_approved', true)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [-90, 90])
            ->whereBetween('longitude', [-180, 180])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
                'phone',
                'website',
                'street',
                'zip',
                'city',
                'logo_path',
                'latitude',
                'longitude',
            ])
            ->map(fn (Organisation $organisation): array => [
                'id' => $organisation->id,
                'name' => $organisation->name,
                'email' => $organisation->email,
                'phone' => $organisation->phone,
                'website' => $organisation->website,
                'website_url' => $this->websiteUrl($organisation->website),
                'street' => $organisation->street,
                'zip' => $organisation->zip,
                'city' => $organisation->city,
                'logo_url' => $organisation->logo_path
                    ? Storage::url($organisation->logo_path)
                    : null,
                'latitude' => $organisation->latitude,
                'longitude' => $organisation->longitude,
            ])
            ->values();

        return view('organisations.map', compact('organisations'));
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

    private function websiteUrl(?string $website): ?string
    {
        $website = trim((string) $website);

        if ($website === '') {
            return null;
        }

        $url = str_contains($website, '://') ? $website : 'https://'.$website;

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
            ? $url
            : null;
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = Organisation::query()
            ->where('is_approved', true)
            ->where('is_active', true);
        $search = trim((string) $request->input('q', ''));
        $category = trim((string) $request->input('kategorie', ''));

        if ($search !== '') {
            $query->where(function (Builder $subQuery) use ($search): void {
                $subQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('zip', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($category !== '' && $category !== 'alle') {
            $query->whereHas('categories', fn (Builder $subQuery): Builder => $subQuery->where('slug', $category));
        }

        return $query;
    }

    private function categoryOptions()
    {
        return Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function searchResult(Organisation $organisation): array
    {
        return [
            'id' => $organisation->id,
            'name' => $organisation->name,
            'ort' => trim(($organisation->zip ?? '').' '.($organisation->city ?? '')),
            'logo_url' => $organisation->logo_path ? Storage::url($organisation->logo_path) : null,
            'categories' => $organisation->categories->pluck('name')->values()->all(),
        ];
    }
}
