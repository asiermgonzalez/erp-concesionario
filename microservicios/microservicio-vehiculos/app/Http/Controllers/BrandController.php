<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    /**
     * Display a listing of the brands.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $brands = Brand::withCount('vehicles')->orderBy('name')->get();
        
        return response()->json($brands);
    }

    /**
     * Store a newly created brand in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:brands',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'country', 'description']);
        
        // Upload logo if provided
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $fileName = Str::slug($request->name) . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('brands', $fileName, 's3');
            $data['logo'] = $path;
        }

        $brand = Brand::create($data);

        return response()->json($brand, 201);
    }

    /**
     * Display the specified brand.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $brand = Brand::with('models')->withCount('vehicles')->findOrFail($id);
        
        return response()->json($brand);
    }

}