<?php

namespace App\Http\Controllers;
use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Invoice;

use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $timeEntries = TimeEntry::whereHas('project.client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json($timeEntries);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        $validatedData = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0', 'max:24'],
            'description' => ['nullable', 'string'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
        ]);

        $projectId = $validatedData['project_id'];
        $projectBelongsToUser = Project::query()
            ->where('id', $projectId)
            ->whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->exists();

        if (!$projectBelongsToUser) {
            abort(403, 'Unauthorised action. The selected project does not belong to you.');
        }

        if (isset($validatedData['invoice_id'])) {
            $invoiceId = $validatedData['invoice_id'];

            $invoiceBelongsToUser = Invoice::query()
                ->where('id', $invoiceId)
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->exists();

            if (!$invoiceBelongsToUser) {
                abort(403, 'Unauthorised action. The selected invoice does not belong to you.');
            }

        }
        $timeEntry = TimeEntry::create($validatedData);

        return response()->json($timeEntry, 201);

    }

    public function show(TimeEntry $timeEntry, Request $request)
    {
        $userId = $request->user()->id;

        if ($timeEntry->project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. You do not own this time entry.');
        }

        return response()->json($timeEntry, 200);

    }

    public function update(TimeEntry $timeEntry, Request $request)
    {
        $userId = $request->user()->id;

        if ($timeEntry->project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. You do not own this time entry.');
        }

        $validatedData = $request->validate([
            'project_id' => ['sometimes', 'integer', 'exists:projects,id'],
            'date' => ['sometimes', 'date'],
            'hours' => ['sometimes', 'numeric', 'min:0', 'max:24'],
            'description' => ['sometimes', 'nullable', 'string'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
        ]);

        if (isset($validatedData['project_id'])) {
            $projectId = $validatedData['project_id'];

            $projectBelongsToUser = Project::query()
                ->where('id', $projectId)
                ->whereHas('client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);

                })->exists();


            if (!$projectBelongsToUser) {
                abort(403, 'Unauthorised action. The selected project does not belong to you.');
            }
        }

        if (isset($validatedData['invoice_id'])) {
            $invoiceId = $validatedData['invoice_id'];

            $invoiceBelongsToUser = Invoice::query()
                ->where('id', $invoiceId)
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);

                })->exists();

            if (!$invoiceBelongsToUser) {
                abort(403, 'Unauthorised action. The selected invoice does not belong to you.');
            }
        }

        $timeEntry->update($validatedData);
        return response()->json($timeEntry, 200);
    }

    public function destroy(TimeEntry $timeEntry, Request $request)
    {
        $userId = $request->user()->id;

        if ($timeEntry->project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. The selected time entry does not belong to you.');
        }


        $timeEntry->delete();

        return response()->json(null, 204);

    }
}
