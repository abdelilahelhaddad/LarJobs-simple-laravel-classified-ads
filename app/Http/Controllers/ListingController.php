<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ListingController extends Controller
{
    public function index()
    {
        return view('listings.index', [
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(6)
        ]);
    }

    public function show(Listing $listing)
    {
        return view('listings.show', [
            'listing' => $listing
        ]);
    }

    public function create()
    {
        return view('listings.create');
    }

    public function store(Request $request)
    {
        $jobsFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        $jobsFields['user_id'] = auth()->id();

        if ($request->hasFile('logo')) {
            $jobsFields['logo'] = $request->file('logo')->store('companylogo', 'public');
        }

        Listing::create($jobsFields);

        return redirect('/')->with('message', 'Jobs created successfully');
    }

    public function edit(Listing $listing)
    {
        return view('listings.edit', [
            'listing' => $listing
        ]);
    }

    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id != auth()->id()) {
            abort(403);
        }

        $jobsFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if ($request->hasFile('logo')) {
            $jobsFields['logo'] = $request->file('logo')->store('companylogo', 'public');
        }

        $listing->update($jobsFields);

        return back()->with('message', 'Jobs updated successfully');
    }

    public function destroy(Listing $listing)
    {
        if ($listing->user_id != auth()->id()) {
            abort(403);
        }
        $listing->delete();
        return redirect('/')->with('message', 'Jobs deleted successfully');
    }

    public function manage()
    {
        return view('listings.manage', [
            'listings' => auth()->user()->listings()->get()
        ]);
    }
}
