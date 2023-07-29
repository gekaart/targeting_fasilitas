@extends('scoring.header')
@section('body_scoring')
@if ($scoring == 'yes')
    <form action="{{ url('/scoring/update_tonaseCIF/'.encrypt($npwp)) }}" method="post">
        @method('put')
    @else
    <form action="{{ url('/scoring/store_tonaseCIF/'.encrypt($npwp)) }}" method="post">
    @endif
    @csrf
        <div class="container mt-5">
            <a href="{{ url(explode("/",request()->path())[0].'/komoditi/'.encrypt($npwp)) }}" class="btn btn-tranparant text-primary">Komoditi</a>
            <a href="{{ url(explode("/",request()->path())[0].'/pemasok/'.encrypt($npwp)) }}" class="btn btn-tranparant text-primary">Pemasok</a>
            <a href="#" class="btn btn-primary ">Tonase dan CIF</a>
        
            <div class="container m-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="40%">HS dan Uraian</th>
                            <th>Tonase dan CIF</th>
                            <th>Pembanding</th>
                            <th width="10%">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        @foreach ($data_awal->unique('HS_CODE') as $i)
                        @php
                            $total_tns = $data_awal->where('HS_CODE', $i->HS_CODE)->sum('NETTO');
                            $total_cif = $data_awal->where('HS_CODE', $i->HS_CODE)->sum('CIF');
                            $harga_per_kg = $total_cif / $total_tns;

                            $total_tns_all = $data_all->where('HS_CODE', $i->HS_CODE)->sum('NETTO');
                            $total_cif_all = $data_all->where('HS_CODE', $i->HS_CODE)->sum('CIF');
                            $harga_per_kg_avg = $total_cif_all / $total_tns_all;

                            // $max_price = $data_all->where('HS_CODE', $i->HS_CODE)->max(CIF);
                            // $max_tns = $data_all->where('HS_CODE', $i->HS_CODE)->where('CIF', $max_price)->pluck('NETTO')[0];
                            // $max =  round($max_price / $max_tns);
                            

                        @endphp
                        <tr>
                            <td>
                                <input type="text" class="form-control-plaintext " name="hs_code[]"
                                    value="{{ $i->HS_CODE}}" readonly>
                                {{$i->UR_BRG }}
                            </td>
                            <td>
                                {{ "Tonase: ". number_format($total_tns/1000,2). " TON" }} <br>
                                {{ "CIF: ". number_format($total_cif)." ".$i->KODE_VALUTA }} <br>
                                {{ "Price/Kg: ". number_format($harga_per_kg,2)." ".$i->KODE_VALUTA }} 
                            
                            </td>
                            <td>
                                Max Price: <br>
                                {{ "Avg Price: ". number_format($harga_per_kg_avg,2)." ".$i->KODE_VALUTA }} <br>
                                Min Price: <br>
                            </td>
                            <td>
                                <select class="form-control" name="sk_tncif[]" required>
                                    @if ( isset($select_sk_tonaseCIF->where('hs_code', $i->HS_CODE)->first()['level']) == "")
                                    <?php 
                                        $cek = $harga_per_kg - $harga_per_kg_avg ;
                                        if ($cek < 0) {
                                            $level = 'Prioritas';
                                            $value = 1;
                                        }
                                        if ($cek >= 0 and $cek < 10 ) {
                                            $level = 'Low';
                                            $value = 2;
                                        }
                                        if ($cek >= 10 and $cek < 50 ) {
                                            $level = 'Medium';
                                            $value = 3;
                                        }
                                        if ($cek >= 50 and $cek < 100 ) {
                                            $level = 'High';
                                            $value = 4;
                                        }
                                        if ($cek >= 100 ) {
                                            $level = 'Very High';
                                            $value = 5;
                                        }

                                    ?>
                                    <option value="{{ $value }}">{{ $level }}</option>
                                        
                                    @else
                                    <?php 
                                        if ($scoring == 'yes') {
                                            // Jika perusaah sudah ada pada database GB maka hs4digit yang digunakan adalah hs4digit milik perusahaan
                                            $select_sk_tonaseCIF = $select_sk_tonaseCIF->OrderBy('id','desc')->where(
                                                [
                                                    'hs_code'=> $i->HS_CODE,
                                                    'npwp_pengusaha'=> $npwp,
                                                ])->first();
                                        } else {
                                            // Jika data beluma ada pada database GB maka hs4digit yang digunakan adalah data terakhir pada database komoditi
                                            $select_sk_tonaseCIF = $select_sk_tonaseCIF->OrderBy('id','desc')->where('hs_code', $i->HS_CODE)->first();
                                        };
                                        
                                        ?>
                                    <option value="{{ $select_sk_tonaseCIF['skor'] }}">{{ $select_sk_tonaseCIF['level'] }}</option>
                                    @endif
                                    <option value="1">Prioritas</option>
                                    <option value="2">Low </option>
                                    <option value="3">Medium </option>
                                    <option value="4">High </option>
                                    <option value="5">Very High </option>
                                </select>
                            </td>
                        </tr>
                        
                    @endforeach
                    </tbody>
                </table>
                @if($count_pemasok != 0)
                    <input type="hidden" name="nama_pengusaha" value="{{ $pengusaha['NAMA_PENGUSAHA'] }}">
                    @if ($scoring == 'no')
                    <input class="btn btn-primary float-end" type="submit" value="simpan">
                    @endif
                    @if ($scoring == 'yes')
                    <input type="hidden" name="url" value="{{ explode("/",request()->path())[0] }}">
                    <input class="btn btn-warning float-end" type="submit" value="update">
                    @endif
                @endif
            </div>

        </div>
        
    </form>
@endsection