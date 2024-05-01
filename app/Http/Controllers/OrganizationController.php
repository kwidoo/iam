<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    // Display a listing of the organizations
    public function index()
    {
        $this->authorize('viewAny', Organization::class);
        $organizations = Organization::all();
        return view('organizations.index', compact('organizations'));
    }

    // Show the form for creating a new organization
    public function create()
    {
        $this->authorize('create', Organization::class);
        return view('organizations.create');
    }

    // Store a newly created organization in storage
    public function store(Request $request)
    {
        $this->authorize('create', Organization::class);
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $organization = Organization::create($data);
        return redirect()->route('organizations.index');
    }

    // Display the specified organization
    public function show(Organization $organization)
    {
        $this->authorize('view', $organization);
        return view('organizations.show', compact('organization'));
    }

    // Show the form for editing the specified organization
    public function edit(Organization $organization)
    {
        $this->authorize('update', $organization);
        return view('organizations.edit', compact('organization'));
    }

    // Update the specified organization in storage
    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $organization->update($data);
        return redirect()->route('organizations.index');
    }

    // Remove the specified organization from storage
    public function destroy(Organization $organization)
    {
        $this->authorize('delete', $organization);
        $organization->delete();
        return redirect()->route('organizations.index');
    }
}
