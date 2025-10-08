<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['client_id', 'name', 'description', 'status', 'start_date', 'end_date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }



}
