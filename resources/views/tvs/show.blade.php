@extends('layouts.config')

@section('content')
<div class="modal fade modal-copy-slide" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kopiraj Slide na drugu lokaciju</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="slide_id" id="">
                <select class="form-select">
                    <option value="" disabled selected>Izaberi</option>
                @foreach ($copyLocations as $copyLocation)
                    <option value="{{$copyLocation->id}}">{{$copyLocation->name}}</option>
                @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
                <button type="button" class="btn btn-primary submitCopySlide">Kopiraj</button>
            </div>
        </div>
    </div>
</div>
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
    @foreach($slides as $slide)
    <div class="col-md-3 mt-3">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        Slide {{$loop->index + 1}}
                    </div>
                    <div class="col text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-warning copy-slide" id="{{$slide->id}}"><i class="bi bi-copy"></i></button>
                            {!! Form::open(['method' => 'POST','id' => $slide->id, 'route' => ['sc.deleteSlide', ['id' => $slide->id]]]) !!}
                                <input type="hidden" name="location_id" value="{{$slide->location_id}}">                                
                                <button type="submit" class="btn btn-sm btn-danger remove-slide" id="{{$slide->id}}"><i class="bi bi-trash"></i></button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <b>{{$slide->slide_title}}</b>
                    </div>
                    <div class="col">
                        <div class="row p-0">
                            @if($slide->slideImages->isEmpty())
                                <div class="col-md-12">
                                    <img src="{{asset('assets/img')}}/placeholder.jpg" style="width:100%;">
                                </div>
                            @else
                                @foreach ($slide->slideImages as $slideImage)
                                    <div class="col-md-4 p-1">
                                        <img src="{{asset('assets/img/uploads').'/'.$slideImage->tv_img}}" style="width:100%;">
                                    </div>
                                @endforeach   
                            @endif                         
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                {!! Form::open(['method' => 'GET','route' => ['sc.editSlide', $slide->id]]) !!}
                    <button type="submit" class="btn btn-sm btn-success edit-slide" id="{{$slide->id}}"><i class="bi bi-pencil-square"></i> Promeni</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="row">
    <div class="col text-end">
        <form action="{{ route('sc.addSlide') }}" method="POST">
            @method('PUT')
            @csrf
            <input type="hidden" name="tv_id" value="{{$tvs->id}}">
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
                    @foreach ($slides as $slide)                    
                        <div class="row p-3 position-absolute mySlidesContainer" style="height: 80%;width:100%;top: 10%; z-index:0;">
                            <div class="col-md-6 mySlidesText-container">
                                <span class="mySlidesText" id="mySlidesText">
                                    <div class="fs-2 mt-4 mb-3">{!! nl2br($slide->slide_title) !!}</div>
                                    <div>{!! nl2br($slide->slide_content) !!}</div> 
                                </span>
                            </div>
                            <div class="col-md-6 mySlidesImage-container">

                                @if($slide->slideImages->isEmpty()) 
                                    <div class="w-100 slide-images" style="position:relative;height:100%;">
                                        <img src="{{asset('assets/img')}}/logo_b.png" style="position:absolute;width:100%;height:100%;object-fit:contain;">
                                    </div>
                                @else
                                    @foreach ($slide->slideImages as $slideImage)
                                        <div class="w-100 slide-images" style="position:relative;height:100%;">
                                            <img src="{{asset('assets/img/uploads')}}/{{$slideImage->tv_img}}" style="position:absolute;width:100%;height:100%;object-fit:contain;">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="row position-absolute bottom-0 w-100" style="margin-bottom:0;margin-left:0;background-color: rgba(0,0,255,0.7);bottom: 0px;height: 10%;">
                        <div class="col text-light fw-bold fs-3 pt-3">
                            <marquee>@if ($tv_marquee != NULL) 
                                {{ $tv_marquee }}
                            @else
                                Ovo su slova koja trče
                            @endif  </marquee>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection