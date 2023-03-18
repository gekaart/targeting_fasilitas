@extends('template.main')
@section('content')
    @switch($content)
        @case('Daftar Pengguna')
            <h1 class="h3 mb-3">{{ $content }}</h1> 
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a class="btn btn-primary" href="users/create" ><i class="align-middle" data-feather="user">  </i>Tambah</a>
                        </div>
                        <div class="card-body">
                           <table id="users_table" class="table table-striped table-hover">
                            <thead>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td></td>
                                    </tr>
                                    @php
                                    $no++;
                                    @endphp
                                @endforeach
                            </tbody>
                           </table>
                       
                        </div>
                    </div>
                </div>
            </div>
    
        @break
        @case("Tambah Pengguna")
            <h1 class="h3 mb-3">{{ $content }}</h1>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Empty card</h5>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>
                </div>
            </div>
        @break
        @case(2)
            
        @break
        @default
            <h1>Halaman yang anda cari tidak ditemukan</h1>
    @endswitch
    

@endsection