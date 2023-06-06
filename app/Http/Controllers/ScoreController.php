<?php

namespace App\Http\Controllers;

use App\Models\DataAwal;
use App\Models\GudangBerikat;
use App\Models\Komoditi;
use App\Models\Pemasok;
use App\Traits\CustomFunctions;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    // Menggunakan custom function (level_score)
    use CustomFunctions;

    // Menampilkan list perusahaan yang akan diskoring
    // public function __invoke()
    // {
    //     return redirect('scoring');
    // }
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
        $data_awal = DataAwal::where('id_pengusaha', $npwp)->get();
        // dd(Komoditi::all());

        if ($count_scoring == 0) {
            // data baru belum masuk ke database gudang berikat
            $data = [
                'content' => 'Skoring Penerima Fasilitas',
                'scoring' => 'no',
                'npwp' => $npwp,
                'pengusaha' => $data_awal->first(),
                'data_komoditi' => $data_awal->unique('HS_CODE'),
                'select_hs4' => Komoditi::latest(),
                // 'data_pemasok' => DataAwal::where('id_pengusaha', $npwp)->get()->unique('NAMA_PEMASOK'),
                // 'tonase' => DataAwal::where('id_pengusaha', $npwp)->sum('netto'),
                // 'cif' => DataAwal::where('id_pengusaha', $npwp)->sum('cif')
            ];
        } else {
            // dd(Komoditi::OrderBy('id', 'desc')->where('empat_digit_hs', '2710')->first());
            //data sudah ada di database gudang berikat
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
        }
        // $sum = DataAwal::where('id_pengusaha', $npwp)->sum('netto');
        // dd($sum);
        return view('scoring', $data);
    }



    public function store(Request $request, $npwp)
    {
        // PROSES DATA KOMODITAS
        $array_no = 0;
        // input skor komoditi ke db komoditi untuk setiap hs4 komoditas
        foreach ($request->hs4_komo as $hs4) {
            // menentukan skore dari level
            $skor = $request['sk_komo'][$array_no];
            $level = $this->level_skor($skor);

            // tambah data komoditi ke database komoditi
            Komoditi::create([
                'npwp_pengusaha' => $npwp,
                'empat_digit_hs' => explode(" - ", $hs4)[0],
                'komoditi' => explode(" - ", $hs4)[1],
                'skor' => $skor,
                'level' => $level,
            ]);
            $array_no++;
        }
        // Hitung rata2 skor komoditi
        $avg_komo = array_sum($request->sk_komo) / count($request->sk_komo);

        //PROSES DATA GUDANG BERIKAT
        $skors = rand(1, 5);
        $status = $this->level_skor($skors);


        // Input data hasil perhitungan pada tabel gudang berikat
        GudangBerikat::create([
            'npwp_pengusaha' => $npwp,
            'nama_pengusaha' => DataAwal::where('id_pengusaha', $npwp)->value('NAMA_PENGUSAHA'),
            'komoditi' => $avg_komo,
            'pemasok' => rand(1, 5),
            'tonase' => rand(1, 5),
            'cif' => rand(1, 5),
            'skors' => rand(1, 5),
            'status' => $status
        ]);

        //UPDATE KOLOM KD DATA AWAL SUDAH DI SKORING
        // Update kolom KD pada tabel data awal menjadi 1 (sudah di skoring)
        DataAwal::where('id_pengusaha', $npwp)->update(['KD' => 1]);

        return redirect(url('scoring/' . $npwp));
    }




    public function store1(Request $request, $npwp)
    {
        // Perhitungan scoring
        $avg_komo = array_sum($request->sk_komo) / count($request->sk_komo);
        // $avg_pmsk = array_sum($request->sk_pmsk) / count($request->sk_pmsk);
        $avg_pmsk = 4;
        $sk_tons = 4;
        $sk_cif = 4;
        // $skor = $avg_komo + $avg_pmsk + $request->sk_tons + $request->sk_cif;
        $skor = $avg_komo + $avg_pmsk + $sk_tons + $sk_cif;
        $skor_akm = $skor / 4;
        if ($skor_akm < 5) {
            $status  = "Layanan Merah";
        } elseif ($skor_akm >= 5 and $skor_akm < 7.5) {
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
            'tonase' => $sk_tons,
            'cif' => $sk_cif,
            'skors' => $skor_akm,
            'status' => $status
        ]);

        $array_no = 0;
        // Input data skoring komoditi pada tabel komoditi
        foreach ($request->sk_komo as $komo) {
            // Menentukan skor dan level dari skor
            $skor = $request['sk_komo'][$array_no];
            switch ($skor) {
                case '1':
                    $level = 'Prioritas';
                    break;
                case '2':
                    $level = 'low';
                    break;
                case '3':
                    $level = 'medium';
                    break;
                case '4':
                    $level = 'high';
                    break;
                case '5':
                    $level = 'very high';
                    break;
            }
            Komoditi::create([
                'npwp_pengusaha' => $npwp,
                'empat_digit_hs' => explode(" - ", $request['hs4_komo'][$array_no], 0),
                'komoditi' => explode(" - ", $request['hs4_komo'][$array_no], 1),
                'skor' => $skor,
                'level' => $level,
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

    public function update(Request $request, $npwp)
    {
        // dd($request);
        // PROSES UPDATE DATA KOMODITAS
        $array_no = 0;
        // update skor komoditi ke db komoditi untuk setiap hs4 komoditas
        foreach ($request->hs4_komo as $hs4) {
            // menentukan skore dari level
            $skor = $request['sk_komo'][$array_no];
            $level = $this->level_skor($skor);

            // ubah data komoditi ke database komoditi
            $hs4_digit = explode(" - ", $hs4)[0];
            Komoditi::where(['npwp_pengusaha' => $npwp, 'empat_digit_hs' => $hs4_digit])->update(
                [
                    'skor' => $skor,
                    'level' => $level,
                ]
            );

            $array_no++;
        }
        // Hitung rata2 skor komoditi
        $avg_komo = array_sum($request->sk_komo) / count($request->sk_komo);


        //PROSES UPDATE DATA GUDANG BERIKAT
        $skors = rand(1, 5);
        $status = $this->level_skor($skors);

        // Update data hasil perhitungan pada tabel gudang berikat
        GudangBerikat::where('npwp_pengusaha', $npwp)->update(
            [
                'komoditi' => $avg_komo,
                'pemasok' => rand(1, 5),
                'tonase' => rand(1, 5),
                'cif' => rand(1, 5),
                'skors' => rand(1, 5),
                'status' => $status
            ]
        );

        // kembali ke menu scoring per perusahaan
        return redirect(url('scoring/' . $npwp));
    }
}
