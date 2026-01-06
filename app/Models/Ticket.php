<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = ['event_id','code','ticket_type'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }
}
