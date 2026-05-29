<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\products;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    /**
     * Show the categories index page Start.================================================
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->withCount('products')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $totalCount = Category::count();

        return view('admin.category.index', compact('categories', 'totalCount'));
    }
    /**
     * Show the categories index page End.================================================
     */

    /**
     * Show the categories add page Start.================================================
     */
    public function add()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('admin.category.category-add', compact('categories'));
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
            'name'  => 'required',
            'image' => 'nullable|string',
        ]);

        // Use submitted slug if provided, otherwise auto-generate
        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        if (Category::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $category = new Category();
        $category->name             = $request->name;
        $category->slug             = $slug;
        $category->is_active        = $request->is_active ?? 1;
        $category->image            = $request->filled('image') ? $request->image : null;
        $category->parent_id        = $request->parent_id ?: null;
        $category->description      = $request->description;
        $category->is_homepage_show  = $request->is_homepage_show ?? 0;
        $category->homepage_category = $request->homepage_category ?? 0;
        $category->is_show_in_menu   = $request->is_show_in_menu ?? 0;
        $category->display_order     = $request->display_order ?? 0;
        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Category added successfully');
    }
    /**
     * Store a newly created resource in storage End.================================================
     */


    /**
     * Show the form for editing the specified category.================================================
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();
        return view('admin.category.category-edit', compact('category', 'categories'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'status' => 'required',
            'image'  => 'nullable|string',
        ]);

        $id = $request->id;
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found');
        }

        $slug = Str::slug($request->name);
        if (Category::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug .= '-' . $id;
        }

        // Determine final image value
        if ($request->input('remove_image') == '1') {
            // AJAX removeImage already deleted the file; just clear the column
            $imageName = null;
        } elseif ($request->filled('image')) {
            // New image picked from media library
            $imageName = $request->image;
        } else {
            // No change — keep existing
            $imageName = $category->image;
        }

        $category->update([
            'name'            => $request->name,
            'slug'            => $slug,
            'is_active'       => $request->status ?? 0,
            'image'           => $imageName,
            'parent_id'       => $request->parent_id ?: null,
            'description'     => $request->description,
            'is_homepage_show'  => $request->is_homepage_show ?? 0,
            'homepage_category' => $request->homepage_category ?? 0,
            'display_order'     => $request->display_order ?? 0,
            'is_show_in_menu'   => $request->is_show_in_menu ?? 0,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully');
    }
    // Update the specified category in storage End.================================================

    // Remove the specified category from storage Start.================================================
    public function delete($id)
    {

        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found');
        }
     if($category->image){
            if(file_exists(public_path('images/category/' . $category->image))){
                unlink(public_path('images/category/' . $category->image));
            }
        }
        $category->delete();



        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully');

    }
    // Remove the specified category from storage End.================================================

    public function removeImage($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image) {
            $path = public_path('images/category/' . $category->image);
            if (file_exists($path)) {
                unlink($path);
            }
            $category->update(['image' => null]);
        }

        return response()->json(['success' => true]);
    }

    public function manageProducts($id){

        $category = Category::find($id);
        $Categoryproducts = $category->products ?? collect();
        $ids = $Categoryproducts?->pluck('id')->toArray() ?? [];
        $products = products::all()->except($ids);
        return view('admin.category.manage-products', compact('category', 'products', 'Categoryproducts'));
    }

    public function assignProducts(Request $request, $id){

        $category = Category::find($id);
        $category->products()->attach($request->products);
        return redirect()->back()->with('status', 'Product added successfully');
    }
    public function unassignProducts(Request $request, $id){
        $category = Category::find($id);
        $category->products()->detach($request->products);
        return redirect()->back()->with('status', 'Product removed successfully');
    }


}

