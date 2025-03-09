<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InteractionController extends Controller
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
     * Display a listing of the interactions.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Interaction::with(['client', 'user']);

        // Apply filters
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('interaction_type')) {
            $query->where('interaction_type', $request->interaction_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('scheduled_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('scheduled_date', '<=', $request->date_to);
        }

        if ($request->boolean('today_only', false)) {
            $query->scheduledToday();
        }

        // Apply sorting
        $sortField = $request->get('sort_field', 'scheduled_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Get paginated results
        $perPage = $request->get('per_page', 15);
        $interactions = $query->paginate($perPage);

        return response()->json($interactions);
    }

    /**
     * Store a newly created interaction in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'user_id' => 'required|integer',
            'interaction_type' => 'required|string|max:50',
            'vehicle_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $interaction = Interaction::create($request->all());

        return response()->json($interaction, 201);
    }

    /**
     * Display the specified interaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $interaction = Interaction::with(['client', 'user'])->findOrFail($id);

        return response()->json($interaction);
    }

    /**
     * Update the specified interaction in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $interaction = Interaction::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id' => 'sometimes|required|exists:clients,id',
            'user_id' => 'sometimes|required|integer',
            'interaction_type' => 'sometimes|required|string|max:50',
            'vehicle_id' => 'nullable|integer',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|string|max:50',
            'scheduled_date' => 'sometimes|required|date',
            'completed_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $interaction->update($request->all());

        return response()->json($interaction);
    }

    /**
     * Update the status of an interaction to completed.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request, $id)
    {
        $interaction = Interaction::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $interaction->status = 'completed';
        $interaction->completed_date = Carbon::now();

        if ($request->has('description')) {
            $interaction->description = $request->description;
        }

        $interaction->save();

        return response()->json($interaction);
    }

    /**
     * Remove the specified interaction from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $interaction = Interaction::findOrFail($id);
        $interaction->delete();

        return response()->json(['message' => 'Interaction deleted successfully']);
    }
}
