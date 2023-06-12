@extends('template.main')
@section('content')

    <h1 class="h3 mb-3">{{ $content }}</h1>
    @switch($content)
        @case('Daftar Gudang Berikat')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kawasan Berikat Selesai Scoring</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="scoring_table">
                            <thead>
                                <th>No</th>
                                <th>Perusahaan</th>
                                <th>NPWP</th>
                                <th>Item Skor</th>
                                <th>Status Layanan</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;   
                                @endphp
                                @foreach ($gudang_berikat as $gb)
                                @php
                                    // Menentukan level komoditi
                                    switch ($gb->komoditi) {
                                        case '1':
                                            $lv_komo = 'Prioritas';
                                            break;
                                        case '2':
                                            $lv_komo = 'Low';
                                            break;
                                        case '3':
                                            $lv_komo = 'Medium';
                                            break;
                                        case '4':
                                            $lv_komo = 'High';
                                            break;
                                        case '5':
                                            $lv_komo = 'Very High';
                                            break;
                                    }

                                    // Menentukan level pemasok
                                    switch ($gb->pemasok) {
                                        case '1':
                                            $lv_pmsk = 'Prioritas';
                                            break;
                                        case '2':
                                            $lv_pmsk = 'Low';
                                            break;
                                        case '3':
                                            $lv_pmsk = 'Medium';
                                            break;
                                        case '4':
                                            $lv_pmsk = 'High';
                                            break;
                                        case '5':
                                            $lv_pmsk = 'Very High';
                                            break;
                                    }

                                @endphp   
                                {{-- Menentukan level komoditi --}}
                                   
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ $gb->nama_pengusaha }}</td>
                                        <td>{{ $gb->npwp_pengusaha }}</td>
                                        <td>
                                            Komoditi :({{ $gb->komoditi }}) {{ $lv_komo }}<br>
                                            Pemasok :({{ $gb->pemasok }}) {{ $lv_pmsk }}<br>
                                        </td>
                                        <td>{{ $gb->status }}</td>
                                        <td>
                                            <a href="{{ url('gudang_berikat/detail/'.$gb->npwp_pengusaha) }}" title="detail"><i class="align-middle text-primary" data-feather="search"></i></a>
                                            <a href="{{ url('gudang_berikat/komoditi/'.$gb->npwp_pengusaha) }}" title="edit"><i class="align-middle text-warning" data-feather="edit"></i></a>
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
        @case(2)
            
            @break
        @default
            
    @endswitch
@endsection