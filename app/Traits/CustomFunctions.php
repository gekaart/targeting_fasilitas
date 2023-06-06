<?php

namespace App\Traits;

trait CustomFunctions
{
    // fungsi untuk mengubah skor menjadi level skor
    function level_skor($skor)
    {
        switch ($skor) {
            case '0':
                $level = '';
                break;
            case '1':
                $level = 'Prioritas';
                break;
            case '2':
                $level = 'Low';
                break;
            case '3':
                $level = 'Medium';
                break;
            case '4':
                $level = 'High';
                break;
            case '5':
                $level = 'Very High';
                break;
        }
        return $level;
    }
}
