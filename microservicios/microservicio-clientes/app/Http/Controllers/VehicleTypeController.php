<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleTypeController extends Controller
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
     * Display a listing of the vehicle types.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $vehicleTypes = VehicleType::all();

        return response()->json($vehicleTypes);
    }

    /**
     * Store a newly created vehicle type in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:vehicle_types',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicleType = VehicleType::create($request->all());

        return response()->json($vehicleType, 201);
    }

    /**
     * Display the specified vehicle type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $vehicleType = VehicleType::findOrFail($id);

        return response()->json($vehicleType);
    }

    /**
     * Update the specified vehicle type in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $vehicleType = VehicleType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100|unique:vehicle_types,name,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicleType->update($request->all());

        return response()->json($vehicleType);
    }

    /**
     * Remove the specified vehicle type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $vehicleType = VehicleType::findOrFail($id);

        // Check if vehicle type is used in client interests
        if ($vehicleType->interestedClients()->count() > 0) {
            return response()->json(['message' => 'Cannot delete vehicle type with associated client interests'], 422);
        }

        $vehicleType->delete();

        return response()->json(['message' => 'Vehicle type deleted successfully']);
    }

    /**
     * Get clients interested in this vehicle type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function interestedClients($id)
    {
        $vehicleType = VehicleType::findOrFail($id);
        $clients = $vehicleType->interestedClients;

        return response()->json($clients);
    }
}
