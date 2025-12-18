<?php

namespace App\Validation;

class MyRules
{
    // Gunakan: end_after[fieldStart]
    public function end_after(string $str, string $fieldStart, array $data, ?string &$error = null): bool
    {
        $start = $data[$fieldStart] ?? null;
        if (!$start || !$str) return true; // biar ditangani rule required/valid_date
        if (strtotime($str) < strtotime($start)) {
            $error = 'Tanggal selesai harus >= tanggal mulai.';
            return false;
        }
        return true;
    }
}
