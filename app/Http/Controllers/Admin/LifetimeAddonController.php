<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\LifetimeAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LifetimeAddonController extends BaseController
{
    /**
     * Display a listing of lifetime addons.
     */
    public function index(Request $request)
    {
        $addons = LifetimeAddon::where('deleted_at', null)
            ->when($request->query('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->query('search') . '%');
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/LifetimeAddon/Index', [
            'title' => __('Lifetime Addons'),
            'allowCreate' => true,
            'rows' => $addons,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show form for creating a new addon.
     */
    public function create()
    {
        return Inertia::render('Admin/LifetimeAddon/Show', [
            'title' => __('Create Lifetime Addon'),
            'addon' => null,
        ]);
    }

    /**
     * Display the specified addon.
     */
    public function show($uuid)
    {
        $addon = LifetimeAddon::where('uuid', $uuid)->firstOrFail();

        return Inertia::render('Admin/LifetimeAddon/Show', [
            'title' => __('Update Lifetime Addon'),
            'addon' => $addon,
        ]);
    }

    /**
     * Store a newly created addon.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'type' => 'required|in:campaign_limit,contacts_limit',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            LifetimeAddon::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'status' => $request->status,
            ]);

            return redirect()
                ->route('admin.lifetime-addons.index')
                ->with('success', __('Addon created successfully!'));
        } catch (\Exception $e) {
            Log::error('Error creating lifetime addon: ' . $e->getMessage());
            return back()->withErrors(['error' => __('Failed to create addon')]);
        }
    }

    /**
     * Update the specified addon.
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'type' => 'required|in:campaign_limit,contacts_limit',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $addon = LifetimeAddon::where('uuid', $uuid)->firstOrFail();

            $addon->update([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'status' => $request->status,
            ]);

            return redirect()
                ->route('admin.lifetime-addons.index')
                ->with('success', __('Addon updated successfully!'));
        } catch (\Exception $e) {
            Log::error('Error updating lifetime addon: ' . $e->getMessage());
            return back()->withErrors(['error' => __('Failed to update addon')]);
        }
    }

    /**
     * Remove the specified addon.
     */
    public function destroy($uuid)
    {
        try {
            $addon = LifetimeAddon::where('uuid', $uuid)->firstOrFail();
            $addon->update(['deleted_at' => now()]);

            return redirect()
                ->route('admin.lifetime-addons.index')
                ->with('success', __('Addon deleted successfully!'));
        } catch (\Exception $e) {
            Log::error('Error deleting lifetime addon: ' . $e->getMessage());
            return back()->withErrors(['error' => __('Failed to delete addon')]);
        }
    }
}
