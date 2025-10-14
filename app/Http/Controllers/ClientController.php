<?php

namespace App\Http\Controllers;
use App\Models\Client;
use Illuminate\Validation\Rule;

//use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {

        $clients = Client::all();
        return response()->json($clients, 200);
    }

    public function store()
    {
        $validatedData = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:clients, email'],
            'address' => ['nullable', 'string'],
        ]);

        //After Implementing Authentication: $client = request()->user()->clients()->create($validatedData);
        $client = Client::create($validatedData);

        return response()->json($client, 201);


    }

    public function show(Client $client)
    {
        return response()->json($client, 200);

    }

    public function update(Client $client)
    {
        $validatedData = request()->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('clients', 'email')->ignore($client->id)],
            'address' => ['sometimes', 'string'],
        ]);

        $client->update($validatedData);

        return response()->json($client, 200);

    }
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(null, 204);
    }
}
