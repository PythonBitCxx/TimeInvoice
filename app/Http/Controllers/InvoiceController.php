<?php

namespace App\Http\Controllers;
use App\Models\Invoice;
use Illuminate\Validation\Rule;
//use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        /*After Auth $userId = request()->user()->id;
        $invoices = Invoice::whereHas('project.client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();*/

        $invoices = Invoice::all();
        return response()->json($invoices, 200);

    }

    public function store()
    {
        $validatedData = request()->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'invoice_number' => ['required', 'string', 'max:255', 'unique:invoices,invoice_number'],
            'issue_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:unpaid,paid,overdue'],
            'paid_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string']
        ]);
        //TODO after auth: make sure project belongs to a client that the current user owns

        $invoice = Invoice::create($validatedData);

        return response()->json($invoice, 201);

    }

    public function show(Invoice $invoice)
    {
        //todo: auth check
        return response()->json($invoice, 200);
    }

    public function update(Invoice $invoice)
    {
        //todo: auth check
        $validatedData = request()->validate([
            'project_id' => ['sometimes', 'integer', 'exists:projects,id'],
            'invoice_number' => ['sometimes', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($invoice->id)],
            'issue_date' => ['sometimes', 'nullable', 'date'],
            'due_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:issue_date'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:unpaid,paid,overdue'],
            'paid_date' => ['sometimes', 'nullable', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $invoice->update($validatedData);

        return response()->json($invoice, 200);

    }

    public function destroy(Invoice $invoice)
    {
        //todo: auth check
        $invoice->delete();

        return response()->json(null, 204);

    }
}
