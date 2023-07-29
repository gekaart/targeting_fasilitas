<?php

namespace App\Http\Controllers;

use App\Models\DataAwal;
use Illuminate\Http\Request;
use App\Models\GudangBerikat;
use App\Models\Komoditi;

class GudangBerikatController extends Controller
{
    // Menampilkan data Perusahaan Kawasan Berikan yang telah selesai di scoring
    public function index()
    {
        $data = [
            'content' => 'Daftar Gudang Berikat',
            'gudang_berikat' => GudangBerikat::all(),
        ];
        return view('gudang_berikat', $data);
    }

    public function detail($npwp)
    {
        $npwp = decrypt($npwp);
        $data = [
            'content' => 'Detail Gudang Berikat',
            'gudang_berikat' => GudangBerikat::where('npwp_pengusaha', $npwp)->get(),
            'data_awal' => DataAwal::all()->where('ID_PENGUSAHA', $npwp),
        ];
        return view('gudang_berikat', $data);
    }
}
