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

    public function edit_komoditi($npwp)
    {
        $data_awal = DataAwal::where('id_pengusaha', $npwp)->get();
        $data = [
            'content' => 'Skoring Penerima Fasilitas',
            'scoring' => 'yes',
            'npwp' => $npwp,
            'pengusaha' => $data_awal->first(),
            'data_komoditi' => $data_awal->unique('HS_CODE'),
            'select_hs4' => Komoditi::latest(),
            'status_gb' => GudangBerikat::where('npwp_pengusaha', $npwp)->pluck('status'),
            // 'data_pemasok' => DataAwal::where('id_pengusaha', $npwp)->get()->unique('NAMA_PEMASOK'),
            // 'tonase' => DataAwal::where('id_pengusaha', $npwp)->sum('netto'),
            // 'cif' => DataAwal::where('id_pengusaha', $npwp)->sum('cif')
        ];
        return view('scoring/komoditi', $data);
    }
}
