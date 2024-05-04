@extends('layouts.config')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Lokacije</h2>
            </div>
            <div class="pull-right">
                @can('tv-create')
                <a class="btn btn-success" href="{{ route('tvs.create') }}"> Napravi novu lokaciju</a>
                @endcan
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Ime Lokacije</th>
            <th>Ulica</th>
            <th>Br.</th>
            <th>Poštanski Br.</th>
            <th>Mesto</th>
            <th>Komentar</th>
            <th width="280px">Opcije</th>
        </tr>
	    @foreach ($tvs as $tv)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $tv->name }}</td>
            <td>{{ $tv->street }}</td>
            <td>{{ $tv->streetno }}</td>
            <td>{{ $tv->postalcode }}</td>
            <td>{{ $tv->city }}</td>
	        <td>{{ $tv->detail }}</td>
	        <td>
                <form action="{{ route('tvs.destroy',$tv->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('tvs.show',$tv->id) }}">Pokaži</a>
                    @can('tv-edit')
                    <a class="btn btn-primary" href="{{ route('tvs.edit',$tv->id) }}">Promeni</a>
                    @endcan

                    @csrf
                    @method('DELETE')
                    @can('tv-delete')
                    <button type="submit" class="btn btn-danger">Izbriši</button>
                    @endcan
                </form>
	        </td>
	    </tr>
	    @endforeach
    </table>

    {!! $tvs->links() !!}
@endsection