<?php

namespace App\Http\Controllers;

use App\Models\DataAwal;
use App\Models\GudangBerikat;
use App\Models\Komoditi;
use App\Models\Pemasok;
use App\Models\TonaseCIF;
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


    // FUNGSI-FUNGSI KOMODITI
    public function komoditi($npwp)
    {
        $npwp = decrypt($npwp);
        // melakukan pengecekan apakah data perusahaan sudah pernah direkam pada data Komoditas
        $count_komoditi = Komoditi::all()->where('npwp_pengusaha', $npwp)->count();
        $data_awal = DataAwal::where('id_pengusaha', $npwp)->get();

        if ($count_komoditi == 0) {
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

    public function store_komoditi(Request $request, $npwp)
    {
        $npwp = decrypt($npwp);
        // PROSES MENYIMPAN DATA KOMODITAS
        $array_no = 0;

        // input skor komoditi ke db komoditi untuk setiap hs4 komoditas
        foreach ($request->hs4_komo as $hs4) {
            // menentukan skore dari level
            $skor = $request['sk_komo'][$array_no];
            $level = $this->level_skor($skor);
            $nama_pengusaha = $request->nama_pengusaha;

            // tambah data komoditi ke database komoditi
            Komoditi::create([
                'npwp_pengusaha' => $npwp,
                'nama_pengusaha' => $nama_pengusaha,
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
        $skors = $avg_komo / 3;
        $status = $this->level_skor(round($skors));


        // Input data hasil perhitungan pada tabel gudang berikat
        GudangBerikat::create([
            'npwp_pengusaha' => $npwp,
            'nama_pengusaha' => $nama_pengusaha,
            'komoditi' => $avg_komo,
            'pemasok' => 0,
            'tonase' => 0,
            'cif' => 0,
            'skors' =>  $skors,
            'status' => $status
        ]);

        //UPDATE KOLOM KD DATA AWAL SUDAH DI SKORING
        // Update kolom KD pada tabel data awal menjadi 1 (sudah di skoring)
        DataAwal::where('id_pengusaha', $npwp)->update(['KD' => 1]);


        return redirect(url('scoring/pemasok/' . encrypt($npwp)));
    }

    public function update_komoditi(Request $request, $npwp)
    {
        $npwp = decrypt($npwp);
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
        $gb_skor = GudangBerikat::where('npwp_pengusaha', $npwp)->first();
        $skors = ($avg_komo + $gb_skor->pemasok + $gb_skor->tonase) / 3;
        $status = $this->level_skor(round($skors));

        // Update data hasil perhitungan pada tabel gudang berikat
        GudangBerikat::where('npwp_pengusaha', $npwp)->update(
            [
                'komoditi' => $avg_komo,
                'skors' => $skors,
                'status' => $status
            ]
        );

        // cek kembali ke menu scoring atau gudang berikat 
        $url = $request->url;
        return redirect(url($url . '/komoditi/' . encrypt($npwp)));
    }


    // FUNGSI-FUNGSI PEMASOK
    public function pemasok($npwp)
    {
        $npwp = decrypt($npwp);
        $data_awal = DataAwal::where('id_pengusaha', $npwp)->get();

        // Cek apakah data pemasok sudah pernah direkam
        $count_pemasok = Pemasok::all()->where('npwp_pengusaha', $npwp)->count();


        if ($count_pemasok == 0) {
            // Jika data pemasok belum ada maka insert data baru
            $data = [
                'content' => 'Skoring Penerima Fasilitas',
                'scoring' => 'no',
                'npwp' => $npwp,
                'pengusaha' => $data_awal->first(),
                'data_awal' => $data_awal,
                'select_sk_pmsk' => Pemasok::latest(),
            ];
        } else {
            // Jika data pemasok sudah ada maka update skoring data pemasok
            $data = [
                'content' => 'Skoring Penerima Fasilitas',
                'scoring' => 'yes',
                'npwp' => $npwp,
                'pengusaha' => $data_awal->first(),
                'data_awal' => $data_awal,
                'data_all' => DataAwal::all('NAMA_PEMASOK', 'HS_CODE', 'NAMA_PENGUSAHA', 'UR_BRG'),
                'select_sk_pmsk' => Pemasok::latest(),
                'status_gb' => GudangBerikat::where('npwp_pengusaha', $npwp)->pluck('status'),
            ];
        }


        return view('scoring/pemasok', $data);
    }

    public function store_pemasok(Request $request, $npwp)
    {
        // PROSES MENYIMPAN DATA PEMASOK
        $npwp = decrypt($npwp);
        $array_no = 0;

        // input skor pemasok ke db pemasok untuk setiap data nama pemasok
        foreach ($request->nm_pmsk as $pmsk) {
            // menentukan skore dari level
            $skor = $request['sk_pmsk'][$array_no];
            $level = $this->level_skor($skor);
            $nama_pengusaha = $request->nama_pengusaha;

            // tambah data pemasok ke database pemasok
            Pemasok::create([
                'npwp_pengusaha' => $npwp,
                'nama_pengusaha' => $nama_pengusaha,
                'pemasok' => $pmsk,
                'negara_asal' => $request['na_pmsk'][$array_no],
                'skor' => $skor,
                'level' => $level,
            ]);
            $array_no++;
        }
        // Hitung rata2 skor pemasok
        $avg_pmsk = array_sum($request->sk_pmsk) / count($request->sk_pmsk);

        //PROSES UPDATE DATA GUDANG BERIKAT
        $sk = GudangBerikat::where('npwp_pengusaha', $npwp)->first();
        $skors = ($avg_pmsk + $sk['komoditi'] + $sk['tonase']) / 3;
        $status = $this->level_skor(round($skors));


        // Update skor pemasok pada database GB
        GudangBerikat::where('npwp_pengusaha', $npwp)->update([
            'npwp_pengusaha' => $npwp,
            'nama_pengusaha' => $nama_pengusaha,
            'pemasok' => $avg_pmsk,
            'skors' =>  $skors,
            'status' => $status
        ]);

        //UPDATE KOLOM KD DATA AWAL SUDAH DI SKORING
        // Update kolom KD pada tabel data awal menjadi 1 (sudah di skoring)
        DataAwal::where('id_pengusaha', $npwp)->update(['KD' => 1]);

        return redirect(url('scoring/tonaseCIF/' . encrypt($npwp)));
    }

    public function update_pemasok(Request $request, $npwp)
    {
        // PROSES UPDATE DATA PEMASOK
        $npwp = decrypt($npwp);
        $array_no = 0;

        // update skor pemasok ke db pemasok untuk setiap nama pemasok
        foreach ($request->nm_pmsk as $pmsk) {
            // menentukan skore dari level
            $skor = $request['sk_pmsk'][$array_no];
            $level = $this->level_skor($skor);

            // ubah data skor dan leve pada data pemasok
            Pemasok::where(['npwp_pengusaha' => $npwp, 'pemasok' => $pmsk])->update(
                [
                    'skor' => $skor,
                    'level' => $level,
                ]
            );

            $array_no++;
        }
        // Hitung rata2 skor pemasok
        $avg_pmsk = array_sum($request->sk_pmsk) / count($request->sk_pmsk);


        //PROSES UPDATE DATA GUDANG BERIKAT
        $gb_skor = GudangBerikat::where('npwp_pengusaha', $npwp)->first();
        $skors = ($avg_pmsk + $gb_skor->pemasok + $gb_skor->tonase) / 3;
        $status = $this->level_skor(round($skors));

        // Update data hasil perhitungan pada tabel gudang berikat
        GudangBerikat::where('npwp_pengusaha', $npwp)->update(
            [
                'pemasok' => $avg_pmsk,
                'skors' => $skors,
                'status' => $status
            ]
        );

        // cek kembali ke menu scoring atau gudang berikat 
        $url = $request->url;
        return redirect(url($url . '/pemasok/' . encrypt($npwp)));
    }

    // FUNGSI-FUNGSI TONASE DAN CIF
    public function tonaseCIF($npwp)
    {
        $npwp = decrypt($npwp);
        $data_awal = DataAwal::where('id_pengusaha', $npwp)->get();
        $count_tonaseCIF = TonaseCIF::all()->where('npwp_pengusaha', $npwp)->count();

        if ($count_tonaseCIF == 0) {
            $data = [
                'content' => 'Skoring Penerima Fasilitas',
                'scoring' => 'no',
                'npwp' => $npwp,
                'pengusaha' => $data_awal->first(),
                'data_awal' => $data_awal,
                'data_all' => DataAwal::all(),
                'select_sk_tonaseCIF' => TonaseCIF::latest(),
                'count_pemasok' => Pemasok::all()->where('npwp_pengusaha', $npwp)->count()
            ];
        } else {
            $data = [
                'content' => 'Skoring Penerima Fasilitas',
                'scoring' => 'yes',
                'npwp' => $npwp,
                'pengusaha' => $data_awal->first(),
                'data_awal' => $data_awal,
                'data_all' => DataAwal::all(),
                'select_sk_tonaseCIF' => TonaseCIF::latest(),
                'count_pemasok' => Pemasok::all()->where('npwp_pengusaha', $npwp)->count(),
                'status_gb' => GudangBerikat::where('npwp_pengusaha', $npwp)->pluck('status'),
            ];
        }
        return view('scoring/tonaseCIF', $data);
    }

    public function store_tonaseCIF(Request $request, $npwp)
    {
        // PROSES MENYIMPAN DATA TONASE CIF
        $npwp = decrypt($npwp);
        $array_no = 0;

        // input skor pemasok ke db tonase CIF untuk setiap data HS Code
        foreach ($request->hs_code as $tncif) {
            // menentukan skore dari level
            $skor = $request['sk_tncif'][$array_no];
            $level = $this->level_skor($skor);
            $nama_pengusaha = $request->nama_pengusaha;

            // tambah data HS Code ke database tonase CIF
            TonaseCIF::create([
                'npwp_pengusaha' => $npwp,
                'nama_pengusaha' => $nama_pengusaha,
                'hs_code' => $tncif,
                'skor' => $skor,
                'level' => $level,
            ]);
            $array_no++;
        }
        // Hitung rata2 skor tonase CIF
        $avg_tncif = array_sum($request->sk_tncif) / count($request->sk_tncif);

        //PROSES UPDATE DATA GUDANG BERIKAT
        $sk = GudangBerikat::where('npwp_pengusaha', $npwp)->first();
        $skors = ($avg_tncif + $sk['komoditi'] + $sk['pemasok']) / 3;
        $status = $this->level_skor(round($skors));


        // Update skor Tonase CIF pada database GB
        GudangBerikat::where('npwp_pengusaha', $npwp)->update([
            'npwp_pengusaha' => $npwp,
            'nama_pengusaha' => $nama_pengusaha,
            'tonase' => $avg_tncif,
            'skors' =>  $skors,
            'status' => $status
        ]);

        //UPDATE KOLOM KD DATA AWAL SUDAH DI SKORING
        // Update kolom KD pada tabel data awal menjadi 1 (sudah di skoring)
        DataAwal::where('id_pengusaha', $npwp)->update(['KD' => 1]);

        return redirect(url('scoring/tonaseCIF/' . encrypt($npwp)));
    }

    public function update_tonaseCIF(Request $request, $npwp)
    {
        // PROSES UPDATE DATA TONASE DAN CIF
        $npwp = decrypt($npwp);
        $array_no = 0;

        // update skor tonase dan cif ke db tonaseCIF untuk setiap HS Code
        foreach ($request->hs_code as $tncif) {
            // menentukan skore dari level
            $skor = $request['sk_tncif'][$array_no];
            $level = $this->level_skor($skor);

            // ubah data skor dan level pada data tonase CIF
            tonaseCIF::where(['npwp_pengusaha' => $npwp, 'hs_code' => $tncif])->update(
                [
                    'skor' => $skor,
                    'level' => $level,
                ]
            );

            $array_no++;
        }
        // Hitung rata2 skor pemasok
        $avg_tncif = array_sum($request->sk_tncif) / count($request->sk_tncif);


        //PROSES UPDATE DATA GUDANG BERIKAT
        $gb_skor = GudangBerikat::where('npwp_pengusaha', $npwp)->first();
        $skors = ($avg_tncif + $gb_skor->pemasok + $gb_skor->komoditi) / 3;
        $status = $this->level_skor(round($skors));

        // Update data hasil perhitungan pada tabel gudang berikat
        GudangBerikat::where('npwp_pengusaha', $npwp)->update(
            [
                'pemasok' => $avg_tncif,
                'skors' => $skors,
                'status' => $status
            ]
        );

        // cek kembali ke menu scoring atau gudang berikat 
        $url = $request->url;
        return redirect(url($url . '/tonaseCIF/' . encrypt($npwp)));
    }
}
