@extends('template.main')
@section('content')
    

        <h1 class="h3 mb-3">{{ $content }}</h1>
        @switch($content)
            @case('Daftar Skoring')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Perusahaan belum di Scoring</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover" id="scoring_table">
                                <thead>
                                    <th>No</th>
                                    <th>Perusahaan</th>
                                    <th>NPWP</th>
                                    <th>Alamat</th>
                                    <th>Kantor Pelayanan</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($perusahaan as $perusahaan)
                                        <tr>
                                            <td>{{ $perusahaan->id }}</td>
                                            <td>{{ $perusahaan->NAMA_PENGUSAHA }}</td>
                                            <td>{{ $perusahaan->ID_PENGUSAHA }}</td>
                                            <td>{{ $perusahaan->ALAMAT_PENGUSAHA }}</td>
                                            <td>{{ $perusahaan->NAMA_KANTOR }}</td>
                                            <td>
                                                <a href="{{ url('scoring/'.$perusahaan->ID_PENGUSAHA) }}" title="scoring"><i class="align-middle text-primary" data-feather="clipboard"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                     
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @break
            @case('Skoring Penerima Fasilitas')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ url('scoring') }}" class="btn btn-secondary btn-sm">Kembali</a>
                        </div>
                        <div class="card-body">
                                                      
                            <div class="container">
                                <h4 >Identitas perusahaan</h4>
                                {{-- @dd($data_pengusaha) --}}
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Nama Perusahaan</th>
                                        <td>{{ $pengusaha['NAMA_PENGUSAHA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>NPWP</th>
                                        <td>{{ $pengusaha['ID_PENGUSAHA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $pengusaha['ALAMAT_PENGUSAHA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kantor Pelayanan</th>
                                        <td>{{ $pengusaha['NAMA_KANTOR'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <form action="{{ url('/scoring/store/'.$npwp) }}" method="post">
                                @csrf
                                <div class="container mt-5">
                                    <h4>Komoditi</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="25%">Komoditi </th>
                                            <th>HS Code dan Uraian Barang</th>
                                            <th width="10%">Skor</th>
                                        </tr>
                                        @foreach ($data_komoditi->unique('EMPAT_DIGIT_HS') as $komo)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control-plaintext" name="hs4_komo[]"
                                                    value="{{ $komo->EMPAT_DIGIT_HS." - ".$komo->KOMODITI }}" readonly>
                                                </td>
                                                <td>
                                                    
                                                    @foreach ($data_komoditi->where('EMPAT_DIGIT_HS', $komo->EMPAT_DIGIT_HS) as $hs)
                                                        {{ $hs->HS_CODE." - ". $hs->UR_BRG }}<br>
                                                    @endforeach
                                                </td>
                                               
                                                <td>
                                                <input class="form-control" type="number" name="sk_komo[]">
                                                </td>
                                            </tr>
                                            
                                        @endforeach


                                    </table>
                                </div>
                                <div class="container mt-5">
                                    <h4>Pemasok</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Nama Pemasok </th>
                                            <th>Negara Asal</th>
                                            <th width="10%">Skor</th>
                                        </tr>
                                        @foreach ($data_pemasok as $pemasok)
                                            <tr>
                                                <td title="{{ $pemasok->ALAMAT_PEMASOK }}">
                                                    <input type="text" class="form-control-plaintext" name="nm_pmsk[]"
                                                    value="{{ $pemasok->NAMA_PEMASOK }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-plaintext" name="na_pmsk[]"
                                                    value="{{ $pemasok->KODE_NEGARA_PEMASOK }}" readonly>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="number" name="sk_pmsk[]">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="container mt-5">
                                    <h4 >Data Lainnya</h4>
                                    {{-- @dd($data_pengusaha) --}}
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="35%">Tonase</th>
                                            <td>{{ $tonase }} TON
                                            </td>
                                            <td width="10%">
                                                <input class="form-control" type="number" name="sk_tons">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>CIF</th>
                                            <td>{{ $cif }} USD</td>
                                            <td>
                                                <input class="form-control" type="number" name="sk_cif">
                                            </td>
                                        </tr>
                                        
                                    </table>
                                </div>
                                <input class="btn btn-primary" type="submit" value="simpan">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @break
            
            @default
                
        @endswitch
@endsection