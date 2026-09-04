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
}
