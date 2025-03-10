<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VehicleController extends Controller
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
     * Display a listing of the vehicles.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Vehicle::with(['brand', 'model']);

        // Apply filters
        if ($request->has('brand_id')) {
            $query->byBrand($request->brand_id);
        }

        if ($request->has('model_id')) {
            $query->byModel($request->model_id);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('min_price') || $request->has('max_price')) {
            $query->byPriceRange($request->min_price, $request->max_price);
        }

        if ($request->has('min_year') || $request->has('max_year')) {
            $query->byYearRange($request->min_year, $request->max_year);
        }

        if ($request->has('min_mileage') || $request->has('max_mileage')) {
            $query->byMileageRange($request->min_mileage, $request->max_mileage);
        }

        if ($request->has('fuel_type')) {
            $query->byFuelType($request->fuel_type);
        }

        if ($request->has('transmission')) {
            $query->byTransmission($request->transmission);
        }

        if ($request->boolean('available', false)) {
            $query->available();
        }

        // Apply search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vin', 'LIKE', "%{$search}%")
                  ->orWhere('registration_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('model', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Check if sorting by related field
        if (in_array($sortField, ['brand', 'model'])) {
            $query->join($sortField . 's', $sortField . 's.id', '=', 'vehicles.' . $sortField . '_id')
                  ->orderBy($sortField . 's.name', $sortDirection)
                  ->select('vehicles.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Get paginated results
        $perPage = $request->get('per_page', 15);
        $vehicles = $query->paginate($perPage);

        // Append main image to each vehicle
        $vehicles->getCollection()->transform(function ($vehicle) {
            $vehicle->main_image = $vehicle->mainImage;
            return $vehicle;
        });

        return response()->json($vehicles);
    }

    /**
     * Store a newly created vehicle in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vin' => 'required|string|max:17|unique:vehicles',
            'registration_number' => 'nullable|string|max:20',
            'brand_id' => 'required|exists:brands,id',
            'model_id' => 'required|exists:vehicle_models,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'mileage' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'condition' => 'required|string|in:new,used,certified',
            'status' => 'required|string|in:available,reserved,sold',
            'fuel_type' => 'required|string|max:50',
            'transmission' => 'required|string|max:50',
            'engine_size' => 'nullable|string|max:50',
            'power' => 'nullable|integer|min:0',
            'doors' => 'nullable|integer|min:0',
            'seats' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'observations' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicle = Vehicle::create($request->all());

        // Create technical specs if provided
        if ($request->has('technical_specs')) {
            $vehicle->technicalSpecs()->create($request->technical_specs);
        }

        return response()->json($vehicle, 201);
    }

    /**
     * Display the specified vehicle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['brand', 'model', 'images', 'technicalSpecs'])->findOrFail($id);
        
        // Add computed properties
        $vehicle->margin = $vehicle->margin;
        $vehicle->margin_percentage = $vehicle->marginPercentage;
        
        return response()->json($vehicle);
    }

    /**
     * Update the specified vehicle in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'vin' => 'sometimes|required|string|max:17|unique:vehicles,vin,' . $id,
            'registration_number' => 'nullable|string|max:20',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'model_id' => 'sometimes|required|exists:vehicle_models,id',
            'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'sometimes|required|string|max:50',
            'mileage' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'cost' => 'sometimes|required|numeric|min:0',
            'condition' => 'sometimes|required|string|in:new,used,certified',
            'status' => 'sometimes|required|string|in:available,reserved,sold',
            'fuel_type' => 'sometimes|required|string|max:50',
            'transmission' => 'sometimes|required|string|max:50',
            'engine_size' => 'nullable|string|max:50',
            'power' => 'nullable|integer|min:0',
            'doors' => 'nullable|integer|min:0',
            'seats' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'observations' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicle->update($request->all());

        // Update technical specs if provided
        if ($request->has('technical_specs')) {
            if ($vehicle->technicalSpecs) {
                $vehicle->technicalSpecs->update($request->technical_specs);
            } else {
                $vehicle->technicalSpecs()->create($request->technical_specs);
            }
        }

        return response()->json($vehicle);
    }

    /**
     * Update the status of a vehicle.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:available,reserved,sold',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicle->status = $request->status;
        $vehicle->save();

        return response()->json($vehicle);
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // Delete related images
        foreach ($vehicle->images as $image) {
            Storage::delete($image->file_path);
            $image->delete();
        }
        
        // Delete technical specs
        if ($vehicle->technicalSpecs) {
            $vehicle->technicalSpecs->delete();
        }
        
        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully']);
    }

    /**
     * Upload images for a vehicle.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImages(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'is_main' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $uploadedImages = [];
        
        foreach ($request->file('images') as $index => $image) {
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $fileSize = $image->getSize();
            $mimeType = $image->getMimeType();
            
            // Generate a unique filename
            $fileName = Str::uuid() . '.' . $extension;
            
            // Store the image
            $path = $image->storeAs('vehicles/' . $vehicle->id, $fileName, 's3');
            
            // Determine if this image should be the main one
            $isMain = false;
            if ($request->has('is_main') && $request->is_main && $index === 0) {
                // If this is the first image and is_main is true
                $isMain = true;
                
                // Set all other images as not main
                $vehicle->images()->update(['is_main' => false]);
            } elseif ($vehicle->images()->count() === 0) {
                // If this is the first image ever for this vehicle
                $isMain = true;
            }
            
            // Create image record
            $vehicleImage = $vehicle->images()->create([
                'file_name' => $fileName,
                'file_path' => $path,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_main' => $isMain,
                'order' => $vehicle->images()->count() + $index,
            ]);
            
            $uploadedImages[] = $vehicleImage;
        }

        return response()->json($uploadedImages, 201);
    }

    /**
     * Set main image for a vehicle.
     *
     * @param  Request  $request
     * @param  int  $id
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMainImage(Request $request, $id, $imageId)
    {
        $vehicle = Vehicle::findOrFail($id);
        $image = VehicleImage::where('vehicle_id', $id)->findOrFail($imageId);
        
        // Set all images as not main
        $vehicle->images()->update(['is_main' => false]);
        
        // Set this image as main
        $image->is_main = true;
        $image->save();

        return response()->json($image);
    }

    /**
     * Delete an image from a vehicle.
     *
     * @param  int  $id
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage($id, $imageId)
    {
        $vehicle = Vehicle::findOrFail($id);
        $image = VehicleImage::where('vehicle_id', $id)->findOrFail($imageId);
        
        // Delete the file
        Storage::delete($image->file_path);
        
        // If this was the main image, set another one as main
        if ($image->is_main) {
            $nextImage = $vehicle->images()
                ->where('id', '!=', $imageId)
                ->orderBy('order')
                ->first();
                
            if ($nextImage) {
                $nextImage->is_main = true;
                $nextImage->save();
            }
        }
        
        // Delete the image record
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    /**
     * Reorder images for a vehicle.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderImages(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:vehicle_images,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update order for each image
        foreach ($request->order as $index => $imageId) {
            VehicleImage::where('id', $imageId)
                ->where('vehicle_id', $id)
                ->update(['order' => $index]);
        }

        return response()->json(['message' => 'Images reordered successfully']);
    }
}
