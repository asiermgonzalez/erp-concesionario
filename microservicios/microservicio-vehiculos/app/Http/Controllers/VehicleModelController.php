<?php

namespace App\Http\Controllers;

use App\Models\VehicleModel;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleModelController extends Controller
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
     * Display a listing of the vehicle models.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = VehicleModel::with('brand')->withCount('vehicles');

        // Filter by brand if specified
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by type if specified
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Apply search if specified
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('type', 'LIKE', "%{$search}%")
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Sort results
        $sortField = $request->get('sort_field', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if ($sortField === 'brand') {
            $query->join('brands', 'brands.id', '=', 'vehicle_models.brand_id')
                ->orderBy('brands.name', $sortDirection)
                ->select('vehicle_models.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Paginate the results
        $perPage = $request->get('per_page', 15);
        $models = $query->paginate($perPage);

        return response()->json($models);
    }

    /**
     * Store a newly created vehicle model in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ensure model name is unique for this brand
        $exists = VehicleModel::where('brand_id', $request->brand_id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json([
                'errors' => [
                    'name' => ['The model name already exists for this brand.']
                ]
            ], 422);
        }

        $model = VehicleModel::create($request->all());

        return response()->json($model, 201);
    }

    /**
     * Display the specified vehicle model.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $model = VehicleModel::with('brand')->withCount('vehicles')->findOrFail($id);

        return response()->json($model);
    }

    /**
     * Update the specified vehicle model in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $model = VehicleModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'brand_id' => 'sometimes|required|exists:brands,id',
            'name' => 'sometimes|required|string|max:100',
            'type' => 'sometimes|required|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ensure model name is unique for this brand
        if ($request->has('name') && ($request->has('brand_id') || $model->brand_id)) {
            $brandId = $request->brand_id ?? $model->brand_id;

            $exists = VehicleModel::where('brand_id', $brandId)
                ->where('name', $request->name)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => [
                        'name' => ['The model name already exists for this brand.']
                    ]
                ], 422);
            }
        }

        $model->update($request->all());

        return response()->json($model);
    }

    /**
     * Remove the specified vehicle model from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $model = VehicleModel::findOrFail($id);

        // Check if the model has associated vehicles
        if ($model->vehicles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete model with associated vehicles'
            ], 422);
        }

        $model->delete();

        return response()->json(['message' => 'Vehicle model deleted successfully']);
    }

    /**
     * Get all vehicles for a specific model.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVehicles($id)
    {
        $model = VehicleModel::findOrFail($id);
        $vehicles = $model->vehicles()->with('brand')->paginate(15);

        return response()->json($vehicles);
    }

    /**
     * Get all available types for vehicle models.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTypes()
    {
        $types = VehicleModel::select('type')
            ->distinct()
            ->orderBy('type')
            ->get()
            ->pluck('type');

        return response()->json($types);
    }
}
