<?php

namespace App\Http\Controllers;

use App\Models\VolunteerListing;

class VolunteerListingController extends Controller
{
    public function index()
    {
        $volunteerListings = VolunteerListing::query()
            ->publiclyVisible()
            ->with(['organisation', 'categories', 'activities'])
            ->latest()
            ->paginate(12);

        return view('volunteer-listings.index', compact('volunteerListings'));
    }

    public function show(VolunteerListing $volunteerListing)
    {
        $volunteerListing = VolunteerListing::query()
            ->publiclyVisible()
            ->with(['organisation', 'categories', 'activities'])
            ->findOrFail($volunteerListing->getKey());

        return view('volunteer-listings.show', compact('volunteerListing'));
    }
}
