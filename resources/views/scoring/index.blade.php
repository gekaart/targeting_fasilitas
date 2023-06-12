@extends('template.main')
@section('content')
    <h1 class="h3 mb-3">{{ $content }}</h1>
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
                            <th class="w-70">Perusahaan</th>
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
                                        <a href="{{ url('scoring/komoditi/'.$perusahaan->ID_PENGUSAHA) }}" title="scoring"><i class="align-middle text-primary" data-feather="clipboard"></i></a>
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
@endsection