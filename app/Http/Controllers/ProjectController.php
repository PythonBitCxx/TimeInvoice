<?php

namespace App\Http\Controllers;
use App\Models\Project;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $projects = Project::whereHas('client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json($projects, 200);

    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,completed,on_hold'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $clientId = $validatedData['client_id'];

        $clientBelongsToUser = $user->clients()->where('id', $clientId)->exists();

        if (!$clientBelongsToUser) {
            abort(403, 'Unauthorised action. The selected client does not belong to you.');

        }
        $project = Project::create($validatedData);

        return response()->json($project, 201);

    }

    public function show(Project $project, Request $request)
    {
        $userId = $request->user()->id;
        if ($project->client->user_id !== $userId) {
            abort(403, 'You do not own this project.');
        }
        return response()->json($project, 200);

    }

    public function update(Project $project, Request $request)
    {
        $userId = $request->user()->id;

        if ($project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. The selected project does not belong to you.');
        }

        $validatedData = $request->validate([
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'in:active,completed,on_hold'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
        ]);

        if (isset($validatedData['client_id'])) {
            $clientId = $validatedData['client_id'];

            $user = $request->user();

            $clientBelongsToUser = $user->clients()->where('id', $clientId)->exists();

            if (!$clientBelongsToUser) {
                abort(403, 'Unauthorised action. The selected client does not belong to you.');
            }
        }
        $project->update($validatedData);

        return response()->json($project, 200);

    }

    public function destroy(Project $project, Request $request)
    {
        $userId = $request->user()->id;

        if ($project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. The selected project does not belong to you.');
        }

        $project->delete();

        return response()->json(null, 204);
    }
}

