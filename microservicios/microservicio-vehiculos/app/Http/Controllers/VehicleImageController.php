<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VehicleImageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get all images for a specific vehicle.
     *
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $images = $vehicle->images()->orderBy('order')->get();
        
        // Add full URL to each image
        $images->transform(function ($image) {
            $image->url = $image->url;
            return $image;
        });
        
        return response()->json($images);
    }

    /**
     * Upload new images for a vehicle.
     *
     * @param  Request  $request
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        
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
            
            // Add URL to the response
            $vehicleImage->url = $vehicleImage->url;
            $uploadedImages[] = $vehicleImage;
        }

        return response()->json($uploadedImages, 201);
    }

    /**
     * Set an image as the main image for a vehicle.
     *
     * @param  int  $vehicleId
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMainImage($vehicleId, $imageId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $image = VehicleImage::where('vehicle_id', $vehicleId)->findOrFail($imageId);
        
        // Set all images as not main
        $vehicle->images()->update(['is_main' => false]);
        
        // Set this image as main
        $image->is_main = true;
        $image->save();

        // Add URL to the response
        $image->url = $image->url;
        
        return response()->json($image);
    }

    /**
     * Delete an image.
     *
     * @param  int  $vehicleId
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($vehicleId, $imageId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $image = VehicleImage::where('vehicle_id', $vehicleId)->findOrFail($imageId);
        
        // Delete the file from storage
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
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:vehicle_images,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if all IDs belong to this vehicle
        $imageCount = VehicleImage::where('vehicle_id', $vehicleId)
            ->whereIn('id', $request->order)
            ->count();
            
        if ($imageCount != count($request->order)) {
            return response()->json(['message' => 'Invalid image IDs provided'], 422);
        }

        // Update order for each image
        foreach ($request->order as $index => $imageId) {
            VehicleImage::where('id', $imageId)
                ->update(['order' => $index]);
        }

        // Get the reordered images
        $images = $vehicle->images()->orderBy('order')->get();
        
        // Add URL to each image
        $images->transform(function ($image) {
            $image->url = $image->url;
            return $image;
        });

        return response()->json([
            'message' => 'Images reordered successfully',
            'images' => $images
        ]);
    }

    /**
     * Update image metadata.
     *
     * @param  Request  $request
     * @param  int  $vehicleId
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMetadata(Request $request, $vehicleId, $imageId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $image = VehicleImage::where('vehicle_id', $vehicleId)->findOrFail($imageId);
        
        $validator = Validator::make($request->all(), [
            'original_name' => 'nullable|string|max:255',
            'is_main' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update metadata
        if ($request->has('original_name')) {
            $image->original_name = $request->original_name;
        }
        
        if ($request->has('is_main') && $request->is_main) {
            // Set all images as not main
            $vehicle->images()->update(['is_main' => false]);
            $image->is_main = true;
        }
        
        $image->save();

        // Add URL to the response
        $image->url = $image->url;
        
        return response()->json($image);
    }

    /**
     * Get the main image for a vehicle.
     *
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMainImage($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $image = $vehicle->images()->where('is_main', true)->first();
        
        if (!$image) {
            return response()->json(['message' => 'No main image found for this vehicle'], 404);
        }
        
        // Add URL to the response
        $image->url = $image->url;
        
        return response()->json($image);
    }
}