<?php

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_code', 5)->nullable()->after('id');
        });

        Event::query()->select('id')->orderBy('id')->chunkById(100, function ($events) {
            foreach ($events as $event) {
                $eventModel = Event::query()->find($event->id);
                if (!$eventModel) {
                    continue;
                }

                $eventModel->event_code = Event::generateUniqueEventCode();
                $eventModel->save();
            }
        });

        Schema::table('events', function (Blueprint $table) {
            $table->unique('event_code');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique(['event_code']);
            $table->dropColumn('event_code');
        });
    }
};
