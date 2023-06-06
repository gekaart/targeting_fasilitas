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
                                    @php
                                        $no = 1;    
                                    @endphp
                                    @foreach ($perusahaan as $perusahaan)
                                        <tr>
                                            <td>{{ $no }}</td>
                                            <td>{{ $perusahaan->NAMA_PENGUSAHA }}</td>
                                            <td>{{ $perusahaan->ID_PENGUSAHA }}</td>
                                            <td>{{ $perusahaan->ALAMAT_PENGUSAHA }}</td>
                                            <td>{{ $perusahaan->NAMA_KANTOR }}</td>
                                            <td>
                                                <a href="{{ url('scoring/'.$perusahaan->ID_PENGUSAHA) }}" title="scoring"><i class="align-middle text-primary" data-feather="clipboard"></i></a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++
                                        @endphp
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
                                    {{-- @dd($status_gb) --}}
                                    @if ($scoring == 'yes')
                                        <tr>
                                            <th>Status Layanan </th>
                                            <td>{{ $status_gb[0] }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                                @if ($scoring == 'yes')
                                    <form action="{{ url('/scoring/update/'.$npwp) }}" method="post">
                                        @method('put')
                                @else
                                    <form action="{{ url('/scoring/store/'.$npwp) }}" method="post">
                                @endif
                                @csrf
                                <div class="container mt-5">
                                    {{-- Kepala Tab --}}
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                          <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" 
                                          data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" 
                                          aria-selected="true">Komoditi</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                          <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" 
                                          data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" 
                                          aria-selected="false">Pemasok</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                          <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" 
                                          data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" 
                                          aria-selected="false">Tonasi dan CIF</button>
                                        </li>
                                        
                                    </ul>
                                      {{-- Body Tab --}}
                                      <div class="tab-content" id="pills-tabContent">
                                        {{-- Body Tab Komoditi --}}
                                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                            <div class="container m-3">
                                                {{-- <h4>Komoditi</h4> --}}
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="30%">Komoditi </th>
                                                        <th>HS Code dan Uraian Barang</th>
                                                        <th width="10%">Skor</th>
                                                    </tr>
                                                    @foreach ($data_komoditi->unique('EMPAT_DIGIT_HS') as $komo)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control-plaintext " name="hs4_komo[]"
                                                                value="{{ $komo->EMPAT_DIGIT_HS." - ".$komo->KOMODITI }}" readonly>
                                                                <!-- Button trigger modal -->
                                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                                    Detail
                                                                </button>
                                                                
                                                            </td>
                                                            <td>
                                                                @foreach ($data_komoditi->where('EMPAT_DIGIT_HS', $komo->EMPAT_DIGIT_HS) as $hs)
                                                                    {{ $hs->HS_CODE." - ". $hs->UR_BRG }}<br>
                                                                @endforeach
                                                            </td>
                                                           
                                                            <td>
                                                            {{-- <input class="form-control" type="number" name="sk_komo[]"> --}}
                                                            
                                                            <select class="form-control" name="sk_komo[]" required>
                                                                @if ( isset($select_hs4->where('empat_digit_hs', $komo->EMPAT_DIGIT_HS)->first()['level']) == "")
                                                                <option value=""></option>
                                                                    
                                                                @else
                                                                <?php 
                                                                    if ($scoring == 'yes') {
                                                                        // Jika perusaah sudah ada pada database GB maka hs4digit yang digunakan adalah hs4digit milik perusahaan
                                                                        $select_hs4 = $select_hs4->OrderBy('id','desc')->where(
                                                                            [
                                                                                'empat_digit_hs'=> $komo->EMPAT_DIGIT_HS,
                                                                                'npwp_pengusaha'=> $npwp,
                                                                            ])->first();
                                                                    } else {
                                                                        // Jika data beluma ada pada database GB maka hs4digit yang digunakan adalah data terakhir pada database komoditi
                                                                        $select_hs4 = $select_hs4->OrderBy('id','desc')->where('empat_digit_hs', $komo->EMPAT_DIGIT_HS)->first();
                                                                    };
                                                                    
                                                                    ?>
                                                                <option value="{{ $select_hs4['skor'] }}">{{ $select_hs4['level'] }}</option>
                                                                @endif
                                                                <option value="1">Prioritas</option>
                                                                <option value="2">Low </option>
                                                                <option value="3">Medium </option>
                                                                <option value="4">High </option>
                                                                <option value="5">Very High </option>
                                                            </select>
                                                            </td>
                                                        </tr>

                                                        <!-- Modal -->
                                                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                ...
                                                                </div>
                                                                <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary">Save changes</button>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endforeach
            
            
                                                </table>
                                            </div>
                                        </div>
                                        {{-- Body Tab Pemasok --}}
                                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                            <div class="container m-3">
                                                {{-- <h4>Pemasok</h4> --}}
                                                    {{-- <table class="table table-bordered">
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
                                                    </table> --}}
                                            </div>
                                        </div>
                                        {{-- Body Tab Tonase dan CIF --}}
                                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                                            <div class="container m-3">
                                                {{-- <h4 >Tonase dan Cif</h4> --}}
                                                {{-- @dd($data_pengusaha) --}}
                                                {{-- <table class="table table-bordered">
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
                                                    
                                                </table> --}}
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">

                                        </div>
                                      </div>
                                      <br>
                                      <br>
                                      <br>
                                      <br>




                                    
                                </div>
                                <div class="container mt-5">
                                    
                                </div>
                                <div class="container mt-5">
                                    
                                </div>
                                @if ($scoring == 'no')
                                <input class="btn btn-primary" type="submit" value="simpan">
                                @endif
                                @if ($scoring == 'yes')
                                <input class="btn btn-warning" type="submit" value="update">
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @break
            
            @default
                
        @endswitch
@endsection