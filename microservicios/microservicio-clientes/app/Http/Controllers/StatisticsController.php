<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Interaction;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
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
     * Get clients by lead source statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientsBySource()
    {
        $stats = Client::select('lead_source', DB::raw('count(*) as total'))
            ->whereNotNull('lead_source')
            ->groupBy('lead_source')
            ->get();
        
        return response()->json($stats);
    }

    /**
     * Get interactions by type statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function interactionsByType()
    {
        $stats = Interaction::select('interaction_type', DB::raw('count(*) as total'))
            ->groupBy('interaction_type')
            ->get();
        
        return response()->json($stats);
    }

    /**
     * Get interests by vehicle type statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function interestsByType()
    {
        $stats = DB::table('client_interests')
            ->join('vehicle_types', 'client_interests.vehicle_type_id', '=', 'vehicle_types.id')
            ->select('vehicle_types.name', DB::raw('count(*) as total'))
            ->groupBy('vehicle_types.name')
            ->get();
        
        return response()->json($stats);
    }

    /**
     * Get client acquisition over time.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientsAcquisition(Request $request)
    {
        $period = $request->get('period', 'month');
        $limit = $request->get('limit', 12);
        
        if ($period === 'month') {
            $stats = Client::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'total' => $item->total
                ];
            });
        } else { // year
            $stats = Client::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('count(*) as total')
            )
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year,
                    'total' => $item->total
                ];
            });
        }
        
        return response()->json($stats);
    }
}
