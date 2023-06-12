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

        return view('scoring/index', $data);
    }

    public function komoditi($npwp)
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
                'komoditi_all' => Komoditi::all(),
                'uraian_all' => DataAwal::where('KD', 1)->get()->unique('UR_BRG'),
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
                'komoditi_all' => Komoditi::all(),
                'uraian_all' => DataAwal::where('KD', 1)->get()->unique('UR_BRG'),
            ];
        }
        // $sum = DataAwal::where('id_pengusaha', $npwp)->sum('netto');
        return view('scoring/komoditi', $data);
    }

    public function pemasok($npwp)
    {
        $data_awal = DataAwal::where('id_pengusaha', $npwp)->get();
        $data = [
            'content' => 'Skoring Penerima Fasilitas',
            'scoring' => 'no',
            'npwp' => $npwp,
            'pengusaha' => $data_awal->first(),
        ];
        return view('scoring/pemasok', $data);
    }

    public function tonaseCIF($npwp)
    {
        $data_awal = DataAwal::where('id_pengusaha', $npwp)->get();
        $data = [
            'content' => 'Skoring Penerima Fasilitas',
            'scoring' => 'no',
            'npwp' => $npwp,
            'pengusaha' => $data_awal->first(),
        ];
        return view('scoring/tonaseCIF', $data);
    }



    public function store_komoditi(Request $request, $npwp)
    {
        // PROSES MENYIMPAN DATA KOMODITAS
        $array_no = 0;
        // input skor komoditi ke db komoditi untuk setiap hs4 komoditas
        foreach ($request->hs4_komo as $hs4) {
            // menentukan skore dari level
            $skor = $request['sk_komo'][$array_no];
            $level = $this->level_skor($skor);
            $nama_pengusaha = $request->nama_pengusaha;

            // tambah data komoditi ke database komoditi
            $komoditi = Komoditi::create([
                'npwp_pengusaha' => $npwp,
                'nama_pengusaha' => $nama_pengusaha,
                'empat_digit_hs' => explode(" - ", $hs4)[0],
                'komoditi' => explode(" - ", $hs4)[1],
                'skor' => $skor,
                'level' => $level,
            ]);
            // dd($komoditi);
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
            'nama_pengusaha' => $nama_pengusaha,
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

        return redirect(url('scoring/komoditi/' . $npwp));
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
