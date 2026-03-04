<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupGate extends Model
{
    protected $fillable = ['gates_id', 'category_id', 'status'];

    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class, 'gates_id');
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'category_id');
    }
}
