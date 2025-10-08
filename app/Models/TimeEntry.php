<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    protected $fillable = ['project_id', 'date', 'hours', 'description', 'invoice_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
