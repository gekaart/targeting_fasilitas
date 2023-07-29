@extends('template.main')
@section('content')
    <h1 class="h3 mb-3">{{ $content }}</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ url(explode("/",request()->path())[0]) }}" class="btn btn-secondary btn-sm">Kembali</a>
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
                            @if ($scoring == 'yes')
                                @php
                                    switch ($status_gb[0]) {
                                        case 'Prioritas':
                                            $text = 'text-primary';
                                            break;
                                        case 'Low':
                                            $text = 'text-success';
                                            break;
                                        case 'Medium':
                                            $text = 'text-warning';
                                            break;
                                        default:
                                            $text = 'text-danger';
                                            break;
                                    }
                                @endphp
                                <tr>
                                    <th>Status Layanan </th>
                                    <td><h1 class="{{ $text }}">{{ $status_gb[0] }}</h1></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    @yield('body_scoring')
                        
                </div>
            </div>
        </div>
    </div>
@endsection