<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientInterestController extends Controller
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
     * Get client interests.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInterests($id)
    {
        $client = Client::findOrFail($id);
        $interests = $client->interests;
        
        return response()->json($interests);
    }

    /**
     * Add a new interest to a client.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addInterest(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'min_year' => 'nullable|integer|min:1900',
            'max_year' => 'nullable|integer|min:1900',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the interest already exists
        if ($client->interests()->where('vehicle_type_id', $request->vehicle_type_id)->exists()) {
            return response()->json(['message' => 'Interest already exists'], 422);
        }

        // Add the interest
        $client->interests()->attach($request->vehicle_type_id, [
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'min_year' => $request->min_year,
            'max_year' => $request->max_year,
        ]);

        return response()->json(['message' => 'Interest added successfully'], 201);
    }

    /**
     * Update client interest.
     *
     * @param  Request  $request
     * @param  int  $clientId
     * @param  int  $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInterest(Request $request, $clientId, $typeId)
    {
        $client = Client::findOrFail($clientId);
        $vehicleType = VehicleType::findOrFail($typeId);
        
        // Check if the interest exists
        if (!$client->interests()->where('vehicle_type_id', $typeId)->exists()) {
            return response()->json(['message' => 'Interest not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'min_year' => 'nullable|integer|min:1900',
            'max_year' => 'nullable|integer|min:1900',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the interest
        $client->interests()->updateExistingPivot($typeId, [
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'min_year' => $request->min_year,
            'max_year' => $request->max_year,
        ]);

        return response()->json(['message' => 'Interest updated successfully']);
    }

    /**
     * Remove an interest from a client.
     *
     * @param  int  $clientId
     * @param  int  $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeInterest($clientId, $typeId)
    {
        $client = Client::findOrFail($clientId);
        
        // Check if the interest exists
        if (!$client->interests()->where('vehicle_type_id', $typeId)->exists()) {
            return response()->json(['message' => 'Interest not found'], 404);
        }

        // Remove the interest
        $client->interests()->detach($typeId);

        return response()->json(['message' => 'Interest removed successfully']);
    }
}