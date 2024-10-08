@extends('layouts.view')
@section('content')
<div class="row m-0 p-0">
        <div class="col m-0 p-0">
            <div class="row m-0 p-0 shadow  position-relative">
                <div class="col m-0 p-0" style="height:100vh;background: url('{{ asset('assets/img/bg_tv.jpg') }}');background-size: 100% auto; background-position: center;background-repeat: no-repeat;">
                    <div class="row m-0 p-0 shadow-sm" style="margin-left:0;margin-top:0;height: 10%;width:100%;z-index:1;">
                        @include('layouts.include.tvheader')
                    </div>                   
                    <div class="row p-3 position-absolute mySlidesContainer" style="height: 80%;width:100%;top: 10%; z-index:0;">
                        <div class="col-md-6 mySlidesText-container">
                            <span class="mySlidesText" id="mySlidesText">
                                <div class="fs-2 mt-4 mb-3 slideTitle"></div>
                                <div class="slideContent"></div> 
                            </span>
                        </div>
                        <div class="col-md-6 mySlidesImage-container">
                            <div class="w-100 slide-images" style="position:relative;height:100%;">
                                
                            </div>
                        </div>
                    </div>
                    <div id="qrcode" class="position-absolute qrcode" style="height: 150px;width:150px;bottom: 12%;right: 20px; z-index:9999999;">
                        Test
                    </div>
                    <div class="row m-0 p-0 position-absolute bottom-0 w-100" style="margin-bottom:0;margin-left:0;background-color: rgba(0,0,255,0.7);bottom: 0px;height: 10%;">
                        <div class="col text-light fw-bold fs-3 pt-3">
                            <marquee>@if ($tv_marquee != NULL) 
                                {{ $tv_marquee }}<img src="{{asset('assets/img/logo.png')}}" style="height:30px;margin-top:-7px">
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