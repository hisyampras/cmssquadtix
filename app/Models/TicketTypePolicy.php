<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTypePolicy extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_type',
        'max_entry_count',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
