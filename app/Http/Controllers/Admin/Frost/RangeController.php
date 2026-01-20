<?php

namespace App\Http\Controllers\Admin\Frost;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Range;
use App\Traits\PageMetaDataTrait;

class RangeController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display a listing of ranges
     */
    public function index(Request $request)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $query = Range::query();

        // Filter by active status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by city
        if ($request->has('city') && $request->city !== '') {
            $query->where('city', $request->city);
        }

        // Search by name
        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'ILIKE', '%' . $request->search . '%');
        }

        $ranges = $query->orderBy('name')->paginate(25);

        // Get unique cities for filter
        $cities = Range::select('city')
                      ->distinct()
                      ->orderBy('city')
                      ->pluck('city');

        $content = array_merge([
            'ranges' => $ranges,
            'cities' => $cities,
            'filters' => [
                'status' => $request->status,
                'city' => $request->city,
                'search' => $request->search,
            ],
        ], self::renderPageMeta('Ranges Management'));

        return view('admin.ranges.index', compact('content'));
    }

    /**
     * Show the form for creating a new range
     */
    public function create()
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $content = array_merge([], self::renderPageMeta('Create Range'));

        return view('admin.ranges.create', compact('content'));
    }

    /**
     * Store a newly created range
     */
    public function store(Request $request)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'inst_name' => 'required|string|max:255',
            'inst_email' => 'nullable|email|max:255',
            'inst_phone' => 'nullable|string|max:16',
            'price' => 'required|numeric|min:0',
            'times' => 'nullable|string|max:64',
            'appt_only' => 'boolean',
            'range_html' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $range = Range::create($validated);

        return redirect()->route('admin.ranges.show', $range)
                        ->with('success', 'Range created successfully.');
    }

    /**
     * Display the specified range
     */
    public function show(Range $range)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $range->load('RangeDates');

        $content = array_merge([
            'range' => $range,
        ], self::renderPageMeta("Range: {$range->name}"));

        return view('admin.ranges.show', compact('content'));
    }

    /**
     * Show the form for editing the specified range
     */
    public function edit(Range $range)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $content = array_merge([
            'range' => $range,
        ], self::renderPageMeta("Edit Range: {$range->name}"));

        return view('admin.ranges.edit', compact('content'));
    }

    /**
     * Update the specified range
     */
    public function update(Request $request, Range $range)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'inst_name' => 'required|string|max:255',
            'inst_email' => 'nullable|email|max:255',
            'inst_phone' => 'nullable|string|max:16',
            'price' => 'required|numeric|min:0',
            'times' => 'nullable|string|max:64',
            'appt_only' => 'boolean',
            'range_html' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $range->update($validated);

        return redirect()->route('admin.ranges.show', $range)
                        ->with('success', 'Range updated successfully.');
    }

    /**
     * Remove the specified range
     */
    public function destroy(Range $range)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $range->delete();

        return redirect()->route('admin.ranges.index')
                        ->with('success', 'Range deleted successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Range $range)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $range->update(['is_active' => !$range->is_active]);

        return back()->with('success', 'Range status updated.');
    }
}
