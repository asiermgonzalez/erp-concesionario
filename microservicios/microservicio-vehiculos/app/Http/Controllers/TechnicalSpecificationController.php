<?php

namespace App\Http\Controllers;

use App\Models\TechnicalSpecification;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TechnicalSpecificationController extends Controller
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
     * Get technical specifications for a specific vehicle.
     *
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $specs = $vehicle->technicalSpecs;
        
        if (!$specs) {
            return response()->json(['message' => 'Technical specifications not found for this vehicle'], 404);
        }
        
        return response()->json($specs);
    }

    /**
     * Create technical specifications for a vehicle.
     *
     * @param  Request  $request
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        
        // Check if specs already exist
        if ($vehicle->technicalSpecs) {
            return response()->json(['message' => 'Technical specifications already exist for this vehicle'], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'engine_type' => 'nullable|string|max:100',
            'displacement' => 'nullable|integer|min:0',
            'cylinders' => 'nullable|integer|min:0',
            'valves' => 'nullable|integer|min:0',
            'power' => 'nullable|integer|min:0',
            'torque' => 'nullable|integer|min:0',
            'acceleration' => 'nullable|numeric|min:0',
            'max_speed' => 'nullable|integer|min:0',
            'fuel_consumption_urban' => 'nullable|numeric|min:0',
            'fuel_consumption_extra' => 'nullable|numeric|min:0',
            'fuel_consumption_combined' => 'nullable|numeric|min:0',
            'co2_emissions' => 'nullable|integer|min:0',
            'emission_standard' => 'nullable|string|max:50',
            'weight' => 'nullable|integer|min:0',
            'length' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'wheelbase' => 'nullable|integer|min:0',
            'trunk_capacity' => 'nullable|integer|min:0',
            'tank_capacity' => 'nullable|integer|min:0',
            'tires' => 'nullable|string|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $specs = new TechnicalSpecification($request->all());
        $vehicle->technicalSpecs()->save($specs);
        
        return response()->json($specs, 201);
    }

    /**
     * Update technical specifications for a vehicle.
     *
     * @param  Request  $request
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $specs = $vehicle->technicalSpecs;
        
        if (!$specs) {
            return response()->json(['message' => 'Technical specifications not found for this vehicle'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'engine_type' => 'nullable|string|max:100',
            'displacement' => 'nullable|integer|min:0',
            'cylinders' => 'nullable|integer|min:0',
            'valves' => 'nullable|integer|min:0',
            'power' => 'nullable|integer|min:0',
            'torque' => 'nullable|integer|min:0',
            'acceleration' => 'nullable|numeric|min:0',
            'max_speed' => 'nullable|integer|min:0',
            'fuel_consumption_urban' => 'nullable|numeric|min:0',
            'fuel_consumption_extra' => 'nullable|numeric|min:0',
            'fuel_consumption_combined' => 'nullable|numeric|min:0',
            'co2_emissions' => 'nullable|integer|min:0',
            'emission_standard' => 'nullable|string|max:50',
            'weight' => 'nullable|integer|min:0',
            'length' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'wheelbase' => 'nullable|integer|min:0',
            'trunk_capacity' => 'nullable|integer|min:0',
            'tank_capacity' => 'nullable|integer|min:0',
            'tires' => 'nullable|string|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $specs->update($request->all());
        
        return response()->json($specs);
    }

    /**
     * Delete technical specifications for a vehicle.
     *
     * @param  int  $vehicleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $specs = $vehicle->technicalSpecs;
        
        if (!$specs) {
            return response()->json(['message' => 'Technical specifications not found for this vehicle'], 404);
        }
        
        $specs->delete();
        
        return response()->json(['message' => 'Technical specifications deleted successfully']);
    }

    /**
     * Compare technical specifications of two vehicles.
     *
     * @param  int  $vehicleId1
     * @param  int  $vehicleId2
     * @return \Illuminate\Http\JsonResponse
     */
    public function compare($vehicleId1, $vehicleId2)
    {
        $vehicle1 = Vehicle::with(['brand', 'model', 'technicalSpecs'])->findOrFail($vehicleId1);
        $vehicle2 = Vehicle::with(['brand', 'model', 'technicalSpecs'])->findOrFail($vehicleId2);
        
        if (!$vehicle1->technicalSpecs || !$vehicle2->technicalSpecs) {
            return response()->json(['message' => 'Technical specifications not found for one or both vehicles'], 404);
        }
        
        // Prepare comparison data
        $comparison = [
            'vehicles' => [
                [
                    'id' => $vehicle1->id,
                    'name' => $vehicle1->brand->name . ' ' . $vehicle1->model->name . ' (' . $vehicle1->year . ')',
                    'image' => $vehicle1->mainImage ? $vehicle1->mainImage->url : null,
                ],
                [
                    'id' => $vehicle2->id,
                    'name' => $vehicle2->brand->name . ' ' . $vehicle2->model->name . ' (' . $vehicle2->year . ')',
                    'image' => $vehicle2->mainImage ? $vehicle2->mainImage->url : null,
                ],
            ],
            'specs' => []
        ];
        
        // List of specifications to compare
        $specsToCompare = [
            'engine_type' => 'Engine Type',
            'displacement' => 'Displacement (cc)',
            'cylinders' => 'Cylinders',
            'valves' => 'Valves',
            'power' => 'Power (HP)',
            'torque' => 'Torque (Nm)',
            'acceleration' => '0-100 km/h (s)',
            'max_speed' => 'Max Speed (km/h)',
            'fuel_consumption_combined' => 'Fuel Consumption (L/100km)',
            'co2_emissions' => 'CO₂ Emissions (g/km)',
            'emission_standard' => 'Emission Standard',
            'weight' => 'Weight (kg)',
            'length' => 'Length (mm)',
            'width' => 'Width (mm)',
            'height' => 'Height (mm)',
            'wheelbase' => 'Wheelbase (mm)',
            'trunk_capacity' => 'Trunk Capacity (L)',
            'tank_capacity' => 'Fuel Tank (L)',
            'tires' => 'Tires',
        ];
        
        // Compare each specification
        foreach ($specsToCompare as $key => $label) {
            $value1 = $vehicle1->technicalSpecs->{$key};
            $value2 = $vehicle2->technicalSpecs->{$key};
            
            // Determine which value is better (higher or lower depending on the spec)
            $better = null;
            if (is_numeric($value1) && is_numeric($value2) && $value1 != $value2) {
                // For these specs, lower is better
                if (in_array($key, ['acceleration', 'fuel_consumption_urban', 'fuel_consumption_extra', 'fuel_consumption_combined', 'co2_emissions'])) {
                    $better = $value1 < $value2 ? 1 : 2;
                } 
                // For these specs, higher is better
                else if (in_array($key, ['power', 'torque', 'max_speed', 'trunk_capacity', 'tank_capacity'])) {
                    $better = $value1 > $value2 ? 1 : 2;
                }
            }
            
            $comparison['specs'][] = [
                'label' => $label,
                'values' => [$value1, $value2],
                'better' => $better
            ];
        }
        
        return response()->json($comparison);
    }
}