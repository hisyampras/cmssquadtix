<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = ['code','no_transaction','name','category_id','other_data'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function categoryRef(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'category_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }
}
