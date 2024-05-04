@extends('layouts.config')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Lokacije</h2>
            </div>
            <div class="pull-right">
                @can('location-create')
                <a class="btn btn-success" href="{{ route('locations.create') }}"> Napravi novu lokaciju</a>
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
	    @foreach ($locations as $location)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $location->name }}</td>
            <td>{{ $location->street }}</td>
            <td>{{ $location->streetno }}</td>
            <td>{{ $location->postalcode }}</td>
            <td>{{ $location->city }}</td>
	        <td>{{ $location->detail }}</td>
	        <td>
                <form action="{{ route('locations.destroy',$location->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('locations.show',$location->id) }}">Pokaži</a>
                    @can('location-edit')
                    <a class="btn btn-primary" href="{{ route('locations.edit',$location->id) }}">Promeni</a>
                    @endcan

                    @csrf
                    @method('DELETE')
                    @can('location-delete')
                    <button type="submit" class="btn btn-danger">Izbriši</button>
                    @endcan
                </form>
	        </td>
	    </tr>
	    @endforeach
    </table>

    {!! $locations->links() !!}
@endsection