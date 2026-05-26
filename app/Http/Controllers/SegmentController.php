<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Segment;
use App\Models\products;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SegmentController extends Controller
{

    /**
     * Show the categories index page Start.================================================
     */
    public function index()
    {
         $categories = Segment::whereNull('parent_id')
        ->with('children') // eager load first level
        ->get();
        return view('admin.segment.index', compact('categories'));
    }
    /**
     * Show the categories index page End.================================================
     */

    /**
     * Show the categories add page Start.================================================
     */
    public function add()
    {
        $categories = Segment::all();
        return view('admin.segment.segment-add', compact('categories'));
    }
    /**
     * Show the categories add page end.================================================
     */


    /**
     * Store a newly created resource in storage Start.================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=> 'required',

        ]);

        // return $request->all();

        // Check if the slug already exists
        $slug = Str::slug($request->name);
        if(Segment::where('slug', $slug)->exists()){
            $slug .= '-';
        }

        $Segment = new Segment();
        $Segment->name = $request->name;
        $Segment->slug = $slug;
        if($request->has('is_active')) {
            $Segment->is_active = $request->is_active;
        }

        if($request->hasFile('image')) {
            // Check if the directory exists
            if(!file_exists(public_path('images/Segment/'))) {
                // Create the directory if it does not exist
                mkdir(public_path('images/Segment/'), 0777, true);
            }

            // Check if the directory has read and write permissions
            if(!is_writable(public_path('images/Segment/'))) {
                chmod(public_path('images/Segment/'), 0777);
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('images/Segment'), $imageName);
            $Segment->image = $imageName;
        }
        if($request->has('parent_id')) {
            $Segment->parent_id = $request->parent_id;
        }
        if($request->has('description')) {
            $Segment->description = $request->description;
        }
        $Segment->save();


        return redirect()->route('admin.segments')->with('success', 'Segment added successfully');
    }
    /**
     * Store a newly created resource in storage End.================================================
     */


    /**
     * Show the form for editing the specified Segment.================================================
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */

    public function edit($id)
    {
        $segment = Segment::find($id);
        return view('admin.segment.segment-edit', compact('segment'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=> 'required',
        ]);
        $Segment = Segment::find($id);

        if (!$Segment) {
            return redirect()->back()->with('error', 'Segment not found');
        }

        // Check if the slug already exists
        $existingSegment = Segment::where('slug', $request->slug)->where('id', '!=', $id)->first();
        if ($existingSegment) {
            $slug = $request->slug . '-' . time();
        }
        // Check if the slug already exists
        $slug = Str::slug($request->name);
        if(Segment::where('slug', $slug)->where('id', '!=', $id)->exists()){
            $slug .= '-';
        }

        $Segment->update([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.segments')->with('success', 'Segment updated successfully');

    }
    // Update the specified Segment in storage End.================================================

    // Remove the specified Segment from storage Start.================================================
    public function delete($id)
    {

        $Segment = Segment::find($id);

        if (!$Segment) {
            return redirect()->back()->with('error', 'Segment not found');
        }
     if($Segment->image){
            if(file_exists(public_path('images/Segment/' . $Segment->image))){
                unlink(public_path('images/Segment/' . $Segment->image));
            }
        }
        $Segment->delete();



        return redirect()->route('admin.segments')->with('success', 'Segment deleted successfully');

    }
    // Remove the specified Segment from storage End.================================================
    public function manageRelation($id){

        $segment = Segment::find($id);
        $segmentProducts = $segment->products ?? collect();
        $ids = $segmentProducts?->pluck('id')->toArray() ?? [];
        $products = products::all()->except($ids);
        return view('admin.segment.manage-relation', compact('segment', 'products', 'SegmentProducts'));
    }

    public function assignProducts(Request $request, $id){

        $segment = Segment::find($id);
        $segment->products()->attach($request->products);
        return redirect()->back()->with('status', 'Product added successfully');
    }
    public function unassignProducts(Request $request, $id){
        $Segment = Segment::find($id);
        $Segment->products()->detach($request->products);
        return redirect()->back()->with('status', 'Product removed successfully');
    }


}

