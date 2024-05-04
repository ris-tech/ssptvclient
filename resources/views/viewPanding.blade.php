@extends('layouts.viewPanding')
@section('content')
<div class="row m-0 p-5">
    <div class="col m-0 p-5">
        <div class="position-absolute top-50 start-50 translate-middle text-center p-5">
            <div  style="background: rgba(255,255,255,0.9);" class="text-center p-5">
                <i class="bi bi-database-fill-gear" style="font-size:5em;"></i><br>
                <span style="font-size:2em;">Podatci se skidaju. Molimo sacekajte!</span>
                
                <div class="progress" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        window.location = window.location;
    }, 10000);
</script>
@endsection