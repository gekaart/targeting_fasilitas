@extends('scoring.header')
@section('body_scoring')
@if ($scoring == 'yes')
    <form action="{{ url('/scoring/update_pemasok/'.encrypt($npwp)) }}" method="post">
        @method('put')
    @else
    <form action="{{ url('/scoring/store_pemasok/'.encrypt($npwp)) }}" method="post">
    @endif
    @csrf
    
        <div class="container mt-5">
            <a href="{{ url(explode("/",request()->path())[0].'/komoditi/'.encrypt($npwp)) }}" class="btn btn-tranparant text-primary">Komoditi</a>
            <a href="#" class="btn btn-primary ">Pemasok</a>
            <a href="{{ url(explode("/",request()->path())[0].'/tonaseCIF/'.encrypt($npwp)) }}" class="btn btn-tranparant text-primary">Tonase dan CIF</a>
        
            <div class="container m-3">
               <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="25%">Pemasok</th>
                        <th width="15%">Negara Asal</th>
                        <th>HS dan Uraian</th>
                        <th width="10%">Skor</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @foreach ($data_awal->unique('NAMA_PEMASOK') as $pmsk)
                        <tr>
                            <td>
                                <input type="text" class="form-control-plaintext " name="nm_pmsk[]"
                                    value="{{ $pmsk->NAMA_PEMASOK }}" readonly>
                                <!-- Button trigger modal -->
                                @if (( isset($select_sk_pmsk->where('pemasok', $pmsk->NAMA_PEMASOK)->first()['level']) != ""))
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#detail{{ $pmsk->id }}">
                                        Detail
                                    </button>
                                @endif
                            </td>
                            <td>
                                <input type="text" class="form-control-plaintext " name="na_pmsk[]"
                                value="{{ $pmsk->KODE_NEGARA_PEMASOK }}" readonly>
                            </td>
                            <td>
                                @foreach ($data_awal->where('NAMA_PEMASOK', $pmsk->NAMA_PEMASOK)->unique('HS_CODE') as $hs)
                                    {{ $hs->HS_CODE." - ". $hs->UR_BRG }}<br>
                                @endforeach

                            </td>
                            <td>
                                <select class="form-control" name="sk_pmsk[]" required>
                                    @if ( isset($select_sk_pmsk->where('pemasok', $pmsk->NAMA_PEMASOK)->first()['level']) == "")
                                    <option value=""></option>
                                        
                                    @else
                                    <?php 
                                        if ($scoring == 'yes') {
                                            // Jika perusaah sudah ada pada database GB maka hs4digit yang digunakan adalah hs4digit milik perusahaan
                                            $select_sk_pmsk = $select_sk_pmsk->OrderBy('id','desc')->where(
                                                [
                                                    'pemasok'=> $pmsk->NAMA_PEMASOK,
                                                    'npwp_pengusaha'=> $npwp,
                                                ])->first();
                                        } else {
                                            // Jika data beluma ada pada database GB maka hs4digit yang digunakan adalah data terakhir pada database komoditi
                                            $select_sk_pmsk = $select_sk_pmsk->OrderBy('id','desc')->where('pemasok', $pmsk->NAMA_PEMASOK)->first();
                                        };
                                        
                                        ?>
                                    <option value="{{ $select_sk_pmsk['skor'] }}">{{ $select_sk_pmsk['level'] }}</option>
                                    @endif
                                    <option value="1">Prioritas</option>
                                    <option value="2">Low </option>
                                    <option value="3">Medium </option>
                                    <option value="4">High </option>
                                    <option value="5">Very High </option>
                                </select>
                            </td>
                        </tr>
                        <!-- Modal Detail-->
                        @if (( isset($select_sk_pmsk->where('pemasok', $pmsk->NAMA_PEMASOK)->first()['level']) != ""))
                            <div class="modal fade " id="detail{{ $pmsk->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h3 class="modal-title" id="exampleModalLabel">Pemasok: {{ $pmsk->NAMA_PEMASOK." (".$pmsk->KODE_NEGARA_PEMASOK.") " }}</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container">
                                            <div class="row border-bottom">
                                            <div class="col-4">
                                                <h4> Perusahaan</h4>
                                            </div>
                                            <div class="col-6">
                                                <h4>HS dan Uraian </h4>
                                            </div>
                                            {{-- <div class="col-2">
                                                <h4>Level</h4>
                                            </div> --}}
                                            </div> 
                                            @foreach ($data_all->where('NAMA_PEMASOK', $pmsk->NAMA_PEMASOK)->unique('NAMA_PENGUSAHA')  as $x)
                                                <div class="row border-bottom">
                                                    <div class="col-4">
                                                         {{ $x->NAMA_PENGUSAHA }}
                                                    </div>
                                                    <div class="col-6">
                                                        @foreach ($data_all->where('NAMA_PEMASOK', $x->NAMA_PEMASOK )
                                                            ->where('NAMA_PENGUSAHA', $x->NAMA_PENGUSAHA )->unique('HS_CODE') as $item)
                                                            {{ $item->HS_CODE." - ".$item->UR_BRG }}<br>
                                                        @endforeach
                                                    </div>
                                                    {{-- <div class="col-2">
                                                        Level
                                                    </div> --}}
                                                </div> 
                                            @endforeach
                                            
                                        </div>
                                        
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
               </table>
               <input type="hidden" name="nama_pengusaha" value="{{ $pengusaha['NAMA_PENGUSAHA'] }}">
               @if ($scoring == 'no' and $pengusaha['KD'] == 1)
               <input class="btn btn-primary float-end" type="submit" value="simpan">
               @endif
               @if ($scoring == 'yes')
               <input type="hidden" name="url" value="{{ explode("/",request()->path())[0] }}">
               <input class="btn btn-warning float-end" type="submit" value="update">
               @endif
            </div>

        </div>
        
    </form>
@endsection