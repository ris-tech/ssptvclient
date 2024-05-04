
@extends('layouts.config')

@section('content')
@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif
<h2 class="mt-3 mb-3 border-bottom">Konfiguracija</h2>
<div class="accordion" id="globalAccordion">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseglobal" aria-expanded="false" aria-controls="collapseglobal">
                Globalna konfiguracija
            </button>
        </h2> 
        <div id="collapseglobal" class="accordion-collapse collapse" data-bs-parent="#globalAccordion">
            <div class="accordion-body">
                <form action="{{ route('tv.updateGlobal',$location) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label fs-4 border-bottom mb-2 w-100" for="tv_marquee">Trčaća slova</label>
                            <textarea id="tv_marquee" rows="6" class="form-control @error('tv_marquee') is-invalid @enderror" name="tv_marquee" placeholder="@if ($tv_marquee != NULL) {{ $tv_marquee }} @else Ovo su slova koja trče @endif" required>@if ($tv_marquee != NULL) {{ $tv_marquee }} @endif</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 text-end">
                            <button class="btn btn-success" name="location_id" value="{{ $location }}">Sačuvaj</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @foreach($tvs as $tv)
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                Slide {{$loop->index + 1}}
            </div>
            <div class="card-body">
                {{$tv->tv_config}}
            </div>
        </div>
    </div>

    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}" aria-expanded="false" aria-controls="collapse{{$loop->index}}">
        
    </button>
    <form action="{{ route('sc.updateSlide',$tv->id) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <input type="hidden" name="tv_id" value="{{ $tv->id }}">
        <input type="hidden" name="location_id" value="{{ $tv->location_id }}">
        <h2 class="mt-3 mb-3 border-bottom">Konfiguracija</h2>
        <div class="row bg-white shadow p-4">
                <div class="col">                
                    <label class="form-label fs-4 border-bottom mb-2 w-100" for="tv_config">Tekst</label>
                    <textarea id="tv_config" class="tv_config form-control @error('tv_config') is-invalid @enderror" name="tv_config" required placeholder="@if ($tv == NULL) Ovde dolazi tekst @endif">@if ($tv != NULL) {{$tv->tv_config}} @endif</textarea>
                </div>
                <div class="col-md-3">
                    <label class="fs-4 border-bottom mb-2 w-100">Slika</label><br>
                    <input type="file" name="tv_img" class="form-control mb-2 @error('tv_img') is-invalid @enderror" @if($tv->tv_img == 'placeholder')required @endif>
                    <img src="@if($tv->tv_img != 'placeholder'){{asset('uploads/'.$tv->tv_img)}}@else{{asset('img/placeholder.jpg')}}@endif" style="height:150px;">
                </div>
            </div>
            <div class="row justify-content-between bg-white shadow p-4">
                <div class="col-2 text-start">
                    <button class="btn btn-danger">Izbriši</button>
                </div>
                <div class="col-2 text-end">
                    <button class="btn btn-success">Sačuvaj</button>
                </div>
        </div>
    </form>
    @endforeach
</div>
<div class="row">
    <div class="col text-end">
        <form action="{{ route('sc.addSlide') }}" method="POST">
            @method('PUT')
            @csrf
            <input type="hidden" name="location_id" value="{{$location}}">
            <button type="submit" name="add_slide" class="btn btn-info">Novi Slide</button>
        </form>
    </div>
</div>
    <div class="row" style="aspect-ratio: 16 / 9;">
        <div class="col">
            <h2 class="mt-3 mb-3 border-bottom">TV</h2>
            <div class="row shadow position-relative" style="height:100%;">
                <div class="col" style="margin:0;padding:0;background: url('{{ asset('assets/img/bg_tv.jpg') }}'); background-position: center;background-repeat: no-repeat;">
                    <div class="row shadow-sm" style="margin-left:0;margin-top:0;height: 10%;width:100%;z-index:1;">
                        @include('layouts.include.tvheader')
                    </div>
                    
                    <div class="row p-3 position-absolute" style="height: 80%;width:100%;top: 10%; z-index:0;">
                        @foreach($tvs as $tv)
                        <div class="col-md-6 shadow mySlidesText w-50" style="z-index:0;">
                            @if ($tv != NULL) 
                                {!! nl2br($tv->tv_config) !!}
                            @else
                                Ovde dolazi tekst
                            @endif
                        </div>
                        <div class="col-md-6 mySlidesImage"  style="height:100%;">
                            <div style="height:500px;">
                                <img src="@if ($tv->tv_img != 'placeholder') {{ asset('uploads/'.$tv->tv_img) }} @else {{ asset('assets/img/placeholder.jpg') }} @endif " style="width:100%;height:100%;object-fit:contain;">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="row position-absolute bottom-0 w-100" style="margin-bottom:0;margin-left:0;background-color: rgba(100,100,100,0.7);bottom: 0px;height: 10%;">
                        <div class="col text-light fw-bold fs-3 pt-3">
                            <marquee>@if ($tv_marquee != NULL) 
                                {{ $tv_marquee }}
                            @else
                                Ovo su slova koja trče
                            @endif  </marquee>
                            <!--<div class="marquee-parent">
                                <div class="marquee-child">
                                                                      
                                </div>
                              </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection