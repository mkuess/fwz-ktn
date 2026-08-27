<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Support\Facades\Storage;

class OrganisationController extends Controller
{
    public function index()
    {
        app()->setLocale('de');

        $organisations = Organisation::where('is_approved', true)
            ->where('is_active', true)
            ->with('categories')
            ->orderBy('name')
            ->paginate(12);

        return view('organisations.index', compact('organisations'));
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
}
