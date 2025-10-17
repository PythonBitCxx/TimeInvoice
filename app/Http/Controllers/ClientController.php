<?php

namespace App\Http\Controllers;
use App\Models\Client;
use Illuminate\Validation\Rule;

//use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {

        //After Implementing Authentication: $clients = request()->user()->clients()->get();
        $clients = Client::all();
        return response()->json($clients, 200);
    }

    public function store()
    {
        $validatedData = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:clients,email'], //todo after auth: scope unique rule to auth user's id
            'address' => ['nullable', 'string'],
        ]);

        //After Implementing Authentication: $client = request()->user()->clients()->create($validatedData);
        $client = Client::create($validatedData);

        return response()->json($client, 201);


    }

    public function show(Client $client)
    {
        //todo: auth check
        return response()->json($client, 200);

    }

    public function update(Client $client)
    {
        //todo: auth check
        $validatedData = request()->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('clients', 'email')->ignore($client->id)],
            'address' => ['sometimes', 'nullable', 'string'],
        ]);

        $client->update($validatedData);

        return response()->json($client, 200);

    }
    public function destroy(Client $client)
    {
        //todo: auth check
        $client->delete();

        return response()->json(null, 204);
    }
}
