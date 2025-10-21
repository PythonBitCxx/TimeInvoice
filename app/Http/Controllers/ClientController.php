<?php

namespace App\Http\Controllers;
use App\Models\Client;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();


        $clients = $user->clients()->get();
        return response()->json($clients, 200);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('clients', 'email')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })
            ],
            'address' => ['nullable', 'string'],
        ]);

        $client = $request->user()->clients()->create($validatedData);

        return response()->json($client, 201);


    }

    public function show(Client $client, Request $request)
    {
        $user = $request->user();
        if ($client->user_id !== $user->id) {
            abort(403, 'Unauthorised action. You do not own this client.');

        }

        return response()->json($client, 200);

    }

    public function update(Client $client, Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        if ($client->user_id !== $user->id) {
            abort(403, 'Unauthorised action. You do not own this client.');
        }

        $validatedData = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                Rule::unique('clients', 'email')
                    ->ignore($client->id)->where(function ($query) use ($userId) {
                        return $query->where('user_id', $userId);
                    })
            ],
            'address' => ['sometimes', 'nullable', 'string'],
        ]);

        $client->update($validatedData);

        return response()->json($client, 200);

    }
    public function destroy(Client $client, Request $request)
    {
        $user = $request->user();

        if ($client->user_id !== $user->id) {
            abort(403, 'Unauthorised action. You do not own this client.');
        }

        $client->delete();

        return response()->json(null, 204);
    }
}
