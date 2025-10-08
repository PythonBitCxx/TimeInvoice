<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['project_id', 'invoice_number', 'issue_date', 'due_date', 'amount', 'status', 'paid_date', 'notes'];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
}
