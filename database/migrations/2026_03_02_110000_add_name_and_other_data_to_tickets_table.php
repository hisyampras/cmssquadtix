<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        $hasName = Schema::hasColumn('tickets', 'name');
        $hasOtherData = Schema::hasColumn('tickets', 'other_data');
        $hasCategoryId = Schema::hasColumn('tickets', 'category_id');
        $hasCategory = Schema::hasColumn('tickets', 'category');

        if (!$hasName) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('name')->nullable()->after('code');
            });
        }

        if (!$hasOtherData) {
            Schema::table('tickets', function (Blueprint $table) use ($hasCategoryId, $hasCategory) {
                if ($hasCategoryId) {
                    $table->text('other_data')->nullable()->after('category_id');
                } elseif ($hasCategory) {
                    $table->text('other_data')->nullable()->after('category');
                } else {
                    $table->text('other_data')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('tickets', 'name')) {
                $drops[] = 'name';
            }
            if (Schema::hasColumn('tickets', 'other_data')) {
                $drops[] = 'other_data';
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
