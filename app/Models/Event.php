<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = ['name','location','start_at','end_at','is_active','event_code'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'is_active'=> 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event): void {
            if (blank($event->event_code)) {
                $event->event_code = self::generateUniqueEventCode();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'event_code';
    }

    public static function generateUniqueEventCode(): string
    {
        do {
            $code = (string) random_int(10000, 99999);
        } while (self::query()->where('event_code', $code)->exists());

        return $code;
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }

    public function ticketTypePolicies(): HasMany
    {
        return $this->hasMany(TicketTypePolicy::class);
    }
}
