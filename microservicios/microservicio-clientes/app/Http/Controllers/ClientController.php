<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
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
     * Display a listing of the clients.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status === 'true' || $request->status === '1');
        }

        if ($request->has('lead_source')) {
            $query->where('lead_source', $request->lead_source);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Get paginated results
        $perPage = $request->get('per_page', 15);
        $clients = $query->paginate($perPage);

        return response()->json($clients);
    }
    

    /**
     * Store a newly created client in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'identification_type' => 'nullable|string|max:50',
            'identification_number' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|boolean',
            'lead_source' => 'nullable|string|max:100',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $client = Client::create($request->all());

        return response()->json($client, 201);
    }

    /**
     * Display the specified client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $client = Client::with(['interactions', 'purchases', 'interests'])->findOrFail($id);

        return response()->json($client);
    }

    /**
     * Update the specified client in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'identification_type' => 'nullable|string|max:50',
            'identification_number' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|boolean',
            'lead_source' => 'nullable|string|max:100',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $client->update($request->all());

        return response()->json($client);
    }

    /**
     * Remove the specified client from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
    }

    /**
     * Get client purchase history.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchaseHistory($id)
    {
        $client = Client::findOrFail($id);
        $purchases = $client->purchases()->with('vehicle')->get();

        return response()->json($purchases);
    }

    /**
     * Get client interaction history.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function interactionHistory($id)
    {
        $client = Client::findOrFail($id);
        $interactions = $client->interactions()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json($interactions);
    }
}
