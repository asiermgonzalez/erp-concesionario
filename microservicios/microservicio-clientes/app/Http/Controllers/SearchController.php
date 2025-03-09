<?php


namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Interaction;
use Illuminate\Http\Request;

class SearchController extends Controller
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
     * Search clients based on criteria.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchClients(Request $request)
    {
        $query = Client::query();

        // Búsqueda general
        if ($request->has('q')) {
            $query->search($request->q);
        }

        // Filtros específicos
        if ($request->has('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        if ($request->has('state')) {
            $query->where('state', 'like', "%{$request->state}%");
        }

        if ($request->has('lead_source')) {
            $query->where('lead_source', $request->lead_source);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status === 'true' || $request->status === '1');
        }

        // Ordenamiento
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $clients = $query->paginate($perPage);

        return response()->json($clients);
    }

    /**
     * Perform advanced search across multiple entities.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function advancedSearch(Request $request)
    {
        // Validar parámetros de búsqueda
        $this->validate($request, [
            'term' => 'required|string|min:2',
            'entities' => 'nullable|array',
            'entities.*' => 'in:clients,interactions',
        ]);

        $term = $request->term;
        $entities = $request->entities ?? ['clients', 'interactions'];
        $results = [];

        // Búsqueda en clientes
        if (in_array('clients', $entities)) {
            $clients = Client::search($term)
                ->select('id', 'first_name', 'last_name', 'email', 'phone')
                ->take(10)
                ->get()
                ->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'type' => 'client',
                        'name' => $client->full_name,
                        'email' => $client->email,
                        'phone' => $client->phone,
                    ];
                });
            
            $results = array_merge($results, $clients->toArray());
        }

        // Búsqueda en interacciones
        if (in_array('interactions', $entities)) {
            $interactions = Interaction::where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->with('client:id,first_name,last_name')
                ->take(10)
                ->get()
                ->map(function ($interaction) {
                    return [
                        'id' => $interaction->id,
                        'type' => 'interaction',
                        'title' => $interaction->title,
                        'client_name' => $interaction->client ? $interaction->client->full_name : 'N/A',
                        'date' => $interaction->scheduled_date->format('Y-m-d'),
                    ];
                });
            
            $results = array_merge($results, $interactions->toArray());
        }

        return response()->json($results);
    }
}