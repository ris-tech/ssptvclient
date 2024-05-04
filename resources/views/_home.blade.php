@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <h1> Lokacije </h1>
    <div class="row">
        @foreach ($tvs as $tv)
            <div class="col-md-3">   
                <a href="{{ route('tvs.edit',$tv->id) }}" class="list-group-item list-group-item-action active">
                    <div class="bg-secondary text-light p-4" style="width: 100%;">
                        <div class="row">
                            <div class="col-2">
                                <img src="{{ asset('assets/img/logo_200.png') }}" style="height:40px;">
                            </div>
                            <div class="col" style="white-space: nowrap; 
                            width: 50px; 
                            overflow: hidden;
                            text-overflow: ellipsis; ">
                                <h5 class="card-title">{{ $tv->name }}</h5>
                                {{ $tv->street }} {{ $tv->streetno }}, {{ $tv->postalcode }} {{ $tv->city }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
