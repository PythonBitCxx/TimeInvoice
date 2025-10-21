<?php

namespace App\Http\Controllers;
use App\Models\Invoice;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Project;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $invoices = Invoice::whereHas('project.client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json($invoices, 200);

    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;


        $validatedData = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('invoices', 'invoice_number')->where(function ($query) use ($userId) {
                    $query->whereHas('project.client', function ($q) use ($userId) {
                        $q->where('user_id', $userId);

                    });

                })
            ],
            'issue_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:unpaid,paid,overdue'],
            'paid_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string']
        ]);

        $projectId = $validatedData['project_id'];

        $project = Project::where('id', $projectId)->whereHas('client', function ($query) use ($userId) {
            $query->where('user_id', $userId);

        })->first();

        if (!$project) {
            abort(403, 'Unauthorised action. The selected project does not belong to you.');
        }
        $invoice = Invoice::create($validatedData);

        return response()->json($invoice, 201);

    }

    public function show(Invoice $invoice, Request $request)
    {
        $userId = $request->user()->id;

        if ($invoice->project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. You do not own this invoice.');
        }

        return response()->json($invoice, 200);
    }

    public function update(Invoice $invoice, Request $request)
    {
        $userId = $request->user()->id;

        if ($invoice->project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. You do not own this invoice.');
        }

        $validatedData = $request->validate([
            'project_id' => ['sometimes', 'integer', 'exists:projects,id'],
            'invoice_number' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('invoices', 'invoice_number')->ignore($invoice->id)
                    ->where(function ($query) use ($userId) {
                        $query->whereHas('project.client', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        });

                    })
            ],
            'issue_date' => ['sometimes', 'nullable', 'date'],
            'due_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:issue_date'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:unpaid,paid,overdue'],
            'paid_date' => ['sometimes', 'nullable', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        if (isset($validatedData['project_id'])) {
            $projectId = $validatedData['project_id'];
            $project = Project::where('id', $projectId)->whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->first();

            if (!$project) {
                abort(403, 'Unauthorised action. The selected project does not belong to you.');
            }
        }

        $invoice->update($validatedData);

        return response()->json($invoice, 200);

    }

    public function destroy(Invoice $invoice, Request $request)
    {
        $userId = $request->user()->id;

        if ($invoice->project->client->user_id !== $userId) {
            abort(403, 'Unauthorised action. You do not own this invoice');

        }

        $invoice->delete();

        return response()->json(null, 204);

    }
}
