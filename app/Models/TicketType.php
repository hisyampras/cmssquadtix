<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketType extends Model
{
    protected $table = 'category';

    protected $fillable = ['events_id', 'category'];

    public function policy(): HasOne
    {
        return $this->hasOne(TicketTypePolicy::class, 'category_id');
    }
}
