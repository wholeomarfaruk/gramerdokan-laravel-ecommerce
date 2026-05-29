<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SegmentController extends Controller
{
    public function index()
    {
        $segments = Segment::withCount('products')->get();
        return view('admin.segment.index', compact('segments'));
    }

    public function add()
    {
        return view('admin.segment.segment-add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->name);
        if (Segment::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        Segment::create([
            'name'        => $request->name,
            'slug'        => $slug,
            'description' => $request->description,
            'is_active'   => $request->input('is_active', 1),
        ]);

        return redirect()->route('admin.segments')->with('success', 'Segment added successfully');
    }

    public function edit(int $id)
    {
        $segment = Segment::findOrFail($id);
        return view('admin.segment.segment-edit', compact('segment'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $segment = Segment::findOrFail($id);

        $slug = Str::slug($request->name);
        if (Segment::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug .= '-' . time();
        }

        $segment->update([
            'name'        => $request->name,
            'slug'        => $slug,
            'description' => $request->description,
            'is_active'   => $request->input('is_active', 1),
        ]);

        return redirect()->route('admin.segments')->with('success', 'Segment updated successfully');
    }

    public function delete(int $id)
    {
        $segment = Segment::withCount('products')->find($id);

        if (!$segment) {
            return response()->json(['success' => false, 'message' => 'Segment not found'], 404);
        }

        if ($segment->products_count > 0) {
            return response()->json([
                'success'       => false,
                'has_relations' => true,
                'message'       => "Cannot delete \"{$segment->name}\". It has {$segment->products_count} product(s) assigned. Remove products first.",
            ]);
        }

        $segment->delete();

        return response()->json(['success' => true, 'message' => 'Segment deleted successfully']);
    }

    public function manageRelation(int $id)
    {
        $segment = Segment::findOrFail($id);
        $segmentProducts = $segment->products()->get();
        $ids = $segmentProducts->pluck('id')->toArray();
        $products = products::whereNotIn('id', $ids)->get();

        return view('admin.segment.manage-relation', compact('segment', 'segmentProducts', 'products'));
    }

    public function assignProducts(Request $request, int $id)
    {
        $segment = Segment::findOrFail($id);
        $segment->products()->attach($request->products);
        return redirect()->back()->with('success', 'Products assigned successfully');
    }

    public function unassignProducts(Request $request, int $id)
    {
        $segment = Segment::findOrFail($id);
        $segment->products()->detach($request->products);
        return redirect()->back()->with('success', 'Product removed successfully');
    }
}
