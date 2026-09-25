<?php

use App\Models\Setting;
use Illuminate\Support\Carbon;

if (! function_exists('get_app_settings')) {
    function get_app_settings(): Setting
    {
        static $settings = null;
        if ($settings === null) {
            $settings = view()->shared('appSettings') ?? Setting::query()->firstOrCreate([]);
        }
        return $settings;
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        $settings = get_app_settings();
        return ($settings->mata_uang ?? 'idr') === 'usd' ? '$' : 'Rp';
    }
}

if (! function_exists('format_currency')) {
    function format_currency(int|float|null $amount, bool $withSymbol = true): string
    {
        if ($amount === null) {
            $amount = 0;
        }

        $settings = get_app_settings();
        $isUsd = ($settings->mata_uang ?? 'idr') === 'usd';

        $isNegative = $amount < 0;
        $absAmount = abs($amount);

        if ($isUsd) {
            $formatted = number_format($absAmount, 0, '.', ',');
            $symbol = '$';
        } else {
            $formatted = number_format($absAmount, 0, ',', '.');
            $symbol = 'Rp';
        }

        $prefix = $isNegative ? '-' : '';
        if (! $withSymbol) {
            return $prefix . $formatted;
        }

        return $prefix . $symbol . ' ' . $formatted;
    }
}

if (! function_exists('format_app_date')) {
    function format_app_date(mixed $date, bool $withTime = false): string
    {
        if (! $date) {
            return '-';
        }

        $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        $settings = get_app_settings();
        $formatType = $settings->format_tanggal ?? 'dd/mm/yyyy';

        if ($formatType === 'yyyy-mm-dd') {
            $format = $withTime ? 'Y-m-d H:i' : 'Y-m-d';
        } else {
            $format = $withTime ? 'd/m/Y H:i' : 'd/m/Y';
        }

        return $carbonDate->format($format);
    }
}

if (! function_exists('format_date_human')) {
    function format_date_human(mixed $date): string
    {
        if (! $date) {
            return '-';
        }

        $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        $settings = get_app_settings();
        $lang = $settings->bahasa ?? 'id';

        $carbonDate->locale($lang);
        return $carbonDate->translatedFormat('d M Y');
    }
}
