@extends('scoring.header')
@section('body_scoring')
@if ($scoring == 'yes')
    <form action="{{ url('/scoring/update_komoditi/'.encrypt($npwp)) }}" method="post">
        @method('put')
    @else
    <form action="{{ url('/scoring/store_komoditi/'.encrypt($npwp)) }}" method="post">
    @endif
    @csrf
        <div class="container mt-5">
            <a href="#" class="btn btn-primary ">Komoditi</a>
            <a href="{{ url(explode("/",request()->path())[0].'/pemasok/'.encrypt($npwp)) }}" class="btn btn-tranparant text-primary">Pemasok</a>
            <a href="{{ url(explode("/",request()->path())[0].'/tonaseCIF/'.encrypt($npwp)) }}" class="btn btn-tranparant text-primary">Tonase dan CIF</a>
        
                <div class="container m-3">
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
                                    @if (( isset($select_hs4->where('empat_digit_hs', $komo->EMPAT_DIGIT_HS)->first()['level']) != ""))
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#detail{{ $komo->EMPAT_DIGIT_HS }}">
                                            Detail
                                        </button>
                                    @endif
                                    
                                </td>
                                <td>
                                    @foreach ($data_komoditi->where('EMPAT_DIGIT_HS', $komo->EMPAT_DIGIT_HS) as $hs)
                                        {{ $hs->HS_CODE." - ". $hs->UR_BRG }}<br>
                                    @endforeach
                                </td>
                            
                                <td>
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

                            <!-- Modal Detail-->
                            @if (( isset($select_hs4->where('empat_digit_hs', $komo->EMPAT_DIGIT_HS)->first()['level']) != ""))
                                <div class="modal fade " id="detail{{ $komo->EMPAT_DIGIT_HS }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title" id="exampleModalLabel">Komoditi: {{ $komo->EMPAT_DIGIT_HS." - ".$komo->KOMODITI }}</h3>
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
                                                <div class="col-2">
                                                    <h4>Level</h4>
                                                </div>
                                                </div> 
                                                @foreach ($komoditi_all->where('empat_digit_hs', $komo->EMPAT_DIGIT_HS) as $komo)
                                                    <div class="row border-bottom">
                                                        <div class="col-4">
                                                            {{ $komo->nama_pengusaha }}
                                                        </div>
                                                        <div class="col-6">
                                                            @foreach ($uraian_all->where('EMPAT_DIGIT_HS',$komo->empat_digit_hs) 
                                                            ->where('ID_PENGUSAHA', $komo->npwp_pengusaha)
                                                            as $hs)
                                                                {{ $hs->HS_CODE." - ". $hs->UR_BRG }}<br>
                                                            @endforeach 
                                                        </div>
                                                        <div class="col-2">
                                                            {{ $komo->level }}({{ $komo->skor }})
                                                        </div>
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


                    </table>
                    
                    <input type="hidden" name="nama_pengusaha" value="{{ $pengusaha['NAMA_PENGUSAHA'] }}">
                    @if ($scoring == 'no')
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