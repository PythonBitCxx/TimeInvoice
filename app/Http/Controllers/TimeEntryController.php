<?php

namespace App\Http\Controllers;
use App\Models\TimeEntry;

//use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    //todo: auth -> index, store, show, update, destroy
    public function index()
    {
        $timeEntries = TimeEntry::all();
        return response()->json($timeEntries);
    }

    public function store()
    {
        $validatedData = request()->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0', 'max:24'],
            'description' => ['nullable', 'string'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
        ]);

        $timeEntry = TimeEntry::create($validatedData);

        return response()->json($timeEntry, 201);

    }

    public function show(TimeEntry $timeEntry)
    {
        return response()->json($timeEntry, 200);

    }

    public function update(TimeEntry $timeEntry)
    {
        $validatedData = request()->validate([
            'project_id' => ['sometimes', 'integer', 'exists:projects,id'],
            'date' => ['sometimes', 'date'],
            'hours' => ['sometimes', 'numeric', 'min:0', 'max:24'],
            'description' => ['sometimes', 'nullable', 'string'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
        ]);

        $timeEntry->update($validatedData);
        return response()->json($timeEntry, 200);
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();

        return response()->json(null, 204);

    }
}
