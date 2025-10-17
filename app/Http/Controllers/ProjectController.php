<?php

namespace App\Http\Controllers;
use App\Models\Project;

//use Illuminate\Http\Request;

class ProjectController extends Controller
{
    //todo: auth -> index, store, show, update, destroy
    public function index()
    {

        $projects = Project::all();
        return response()->json($projects, 200);

    }

    public function store()
    {
        $validatedData = request()->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,completed,on_hold'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $project = Project::create($validatedData);

        return response()->json($project, 201);

    }

    public function show(Project $project)
    {
        return response()->json($project, 200);

    }

    public function update(Project $project)
    {
        $validatedData = request()->validate([
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'in:active,completed,on_hold'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $project->update($validatedData);

        return response()->json($project, 200);

    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(null, 204);
    }
}

