<?php

namespace App\Http\Controllers;

use App\Models\DataAwal;
use App\Models\GudangBerikat;
use App\Models\Komoditi;
use App\Models\Pemasok;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    // Menampilkan list perusahaan yang akan diskoring
    public function index()
    {
        $data = [
            'content' => 'Daftar Skoring',
            'perusahaan' => DataAwal::all()->where('KD', 0)->unique('ID_PENGUSAHA')
        ];

        return view('scoring', $data);
    }

    public function show($npwp)
    {
        // melakukan pengecekan apakah data perusahaan sudah pernah dilakukan skoring
        $count_scoring = GudangBerikat::all()->where('npwp_pengusaha', $npwp)->count();
        $data_awal = DataAwal::where('id_pengusaha', $npwp);

        if ($count_scoring == 0) {

            $data = [
                'content' => 'Skoring Penerima Fasilitas',
                'scoring' => 'no',
                'npwp' => $npwp,
                'pengusaha' => DataAwal::where('id_pengusaha', $npwp)->first(),
                'data_komoditi' => DataAwal::where('id_pengusaha', $npwp)->get()->unique('HS_CODE'),
                'data_pemasok' => DataAwal::where('id_pengusaha', $npwp)->get()->unique('NAMA_PEMASOK'),
                'tonase' => DataAwal::where('id_pengusaha', $npwp)->sum('netto'),
                'cif' => DataAwal::where('id_pengusaha', $npwp)->sum('cif')
            ];
        } else {
            $data = [
                'content' => 'Skoring Penerima Fasilitas',
                'scoring' => 'yes',
                'npwp' => $npwp,
                'pengusaha' => DataAwal::where('id_pengusaha', $npwp)->first(),
                'data_komoditi' => DataAwal::where('id_pengusaha', $npwp)->get()->unique('HS_CODE'),
                'data_pemasok' => DataAwal::where('id_pengusaha', $npwp)->get()->unique('NAMA_PEMASOK'),
                'tonase' => DataAwal::where('id_pengusaha', $npwp)->sum('netto'),
                'cif' => DataAwal::where('id_pengusaha', $npwp)->sum('cif')
            ];
        }
        // $sum = DataAwal::where('id_pengusaha', $npwp)->sum('netto');
        // dd($sum);
        return view('scoring', $data);
    }

    public function store(Request $request, $npwp)
    {
        // Perhitungan scoring
        $avg_komo = array_sum($request->sk_komo) / count($request->sk_komo);
        $avg_pmsk = array_sum($request->sk_pmsk) / count($request->sk_pmsk);
        $skor = $avg_komo + $avg_pmsk + $request->sk_tons + $request->sk_cif;
        $skor_akm = $skor / 4;
        if ($skor_akm < 50) {
            $status  = "Layanan Merah";
        } elseif ($skor_akm >= 50 and $skor_akm < 75) {
            $status  = "Layanan kuning";
        } else {
            $status = "Layanan Hijau";
        }

        // Input data hasil perhitungan pada tabel gudang berikat
        GudangBerikat::create([
            'npwp_pengusaha' => $npwp,
            'nama_pengusaha' => DataAwal::where('id_pengusaha', $npwp)->value('NAMA_PENGUSAHA'),
            'komoditi' => $avg_komo,
            'pemasok' => $avg_pmsk,
            'tonase' => $request->sk_tons,
            'cif' => $request->sk_cif,
            'skors' => $skor_akm,
            'status' => $status
        ]);

        $array_no = 0;
        // Input data skoring komoditi pada tabel komoditi
        foreach ($request->sk_komo as $komo) {
            Komoditi::create([
                'npwp' => $npwp,
                'hs4_komoditi' => $request['hs4_komo'][$array_no],
                'hs_komoditi' => $request['hs_komo'][$array_no],
                'uraian_komoditi' => $request['ur_komo'][$array_no],
                'score' => $komo[$array_no],
            ]);
            $array_no++;
        }

        // Input data skoring pemasok pada tabel pemasok
        foreach ($request->sk_pmsk as $pmsk) {
            Pemasok::create([
                'npwp' => $npwp,
                'nama_pemasok' => $request['nm_pmsk'][$array_no],
                'negara_asal' => $request['na_pmsk'][$array_no],
                'score' => $pmsk[$array_no],
            ]);
            $array_no++;
        }

        //Update kolom KD pada tabel data awal menjadi 1 (sudah di skoring)
        DataAwal::where('id_pengusaha', $npwp)->update(['KD' => 1]);
    }
}
