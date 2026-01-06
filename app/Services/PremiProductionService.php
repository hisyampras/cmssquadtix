<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PremiProductionService
{
    /**
     * (Tetap ada untuk kompatibilitas lama) → default KONVEN.
     */
    public function fetchPremi(Carbon $current, Carbon $start, Carbon $end): Collection
    {
        return $this->fetchPremiBySegment('konven', $current, $start, $end);
    }

    /**
     * Ambil data premi berdasar segment: 'konven' | 'syariah'.
     * Akan memanggil SP:
     *  - dbo.generate_data_tb_premi_konven
     *  - dbo.generate_data_tb_premi_syariah
     */
    public function fetchPremiBySegment(string $segment, Carbon $current, Carbon $start, Carbon $end): Collection
{
    $segment = strtolower($segment) === 'syariah' ? 'syariah' : 'konven';

    if ($segment === 'syariah') {
        // SP syariah: @StartDate, @EndDate, @CurrentDate
        $spName = 'dbo.generate_data_tb_premi_syariah';
        $params = [$start->toDateString(), $end->toDateString(), $current->toDateString()];
    } else {
        // SP konven: @current_date, @start_date, @end_date
        $spName = 'dbo.generate_data_tb_premi_konven';
        $params = [$current->toDateString(), $start->toDateString(), $end->toDateString()];
    }

    $rows = DB::connection('secondary_sqlsrv')->select(
        "EXEC {$spName} ?, ?, ?",
        $params
    );

    return collect($rows)->map(function ($r) {
        $arr = (array) $r;
        $arr = array_change_key_case($arr, CASE_UPPER);

        foreach ([
            'PREMI_OR','PREMI_BRUTO','PREMI_TABARU','PREMI_UJROH',
            'DISKON','KOMISI','PREMI_REAS','KOMISI_REAS','CUR_RATE','TSI','SUM_INSURED'
        ] as $k) {
            if (isset($arr[$k])) $arr[$k] = (float) $arr[$k];
        }

        if (!empty($arr['CLOSE_DT'])) {
            $arr['CLOSE_DT'] = Carbon::parse($arr['CLOSE_DT'])->startOfDay();
        }

        return $arr;
    });
}
}
