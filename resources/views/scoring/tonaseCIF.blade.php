@extends('scoring.header')
@section('body_scoring')
@if ($scoring == 'yes')
    <form action="{{ url('/scoring/update/'.$npwp) }}" method="post">
        @method('put')
    @else
    <form action="{{ url('/scoring/store/'.$npwp) }}" method="post">
    @endif
    @csrf
        <div class="container mt-5">
            <a href="{{ url(explode("/",request()->path())[0].'/komoditi/'.$npwp) }}" class="btn btn-tranparant text-primary">Komoditi</a>
            <a href="{{ url(explode("/",request()->path())[0].'/pemasok/'.$npwp) }}" class="btn btn-tranparant text-primary">Pemasok</a>
            <a href="#" class="btn btn-primary ">Tonase dan CIF</a>
        
            <div class="container m-3">
                Komoditi CIF
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
@endsection