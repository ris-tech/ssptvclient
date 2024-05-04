@extends('layouts.config')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Izmena lokacije</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('tvs.index') }}"> Nazad</a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Opa!</strong> Bila je greška u unosu!<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tvs.update',$tv->id) }}" method="POST">
    	@csrf
        @method('PUT')
         <div class="row">
		    <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>Ime lokacije:</strong>
		            <input type="text" name="name" value="{{ $tv->name }}" class="form-control" placeholder="Ime lokacije">
		        </div>
		    </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>Ulica:</strong>
		            <input type="text" name="street" value="{{ $tv->street }}" class="form-control" placeholder="Ulica">
		        </div>
		    </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>Broj:</strong>
		            <input type="text" name="streetno" value="{{ $tv->streetno }}" class="form-control" placeholder="Broj">
		        </div>
		    </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>Poštanski Broj:</strong>
		            <input type="text" name="postalcode" value="{{ $tv->postalcode }}" class="form-control" placeholder="Poštanski Broj">
		        </div>
		    </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>Grad:</strong>
		            <input type="text" name="city" value="{{ $tv->city }}" class="form-control" placeholder="Grad">
		        </div>
		    </div>
		    <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>Komentar:</strong>
		            <textarea class="form-control" style="height:150px" name="detail" placeholder="Komentar">{{ $tv->detail }}</textarea>
		        </div>
		    </div>
		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
		      <button type="submit" class="btn btn-primary">Sačuvaj</button>
		    </div>
		</div>
    </form>

@endsection