<?php

if (!function_exists('idr')) {
    function idr(float $n): string {
        return 'Rp '.number_format($n, 0, ',', '.');
    }
}
