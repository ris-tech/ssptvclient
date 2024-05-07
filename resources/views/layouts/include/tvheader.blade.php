<div class="col-1 p3" style="display: flex;justify-content: center;align-items: center;">
    <img class="tv_logo" style="max-height:70px;" src="{{ asset('assets/img/logo.png') }}">
</div>
<div class="col-5 pt4 ps5" style="display: flex;align-items: center;">
    <div class="fs-1">Синдикат српске полиције</div>
</div>
<div class="col-2" style="align-items: center;">
    <div class="fs-5">{{ Str::lat2cyr(Str::datenames(date('N'))) }}<br><span class="fs-5 datecont">{{ date('d.m.Y') }}</span>&nbsp;&nbsp;<span class="fs-5 timecont">{{ date('H:i') }}</span></div> 
</div>
<div class="col-4 text-end text-light" style="max-height:100%;">
    <div class="row">
            <div class="col weather-place text-dark pt-3 fs-4"></div>
            <div class="col text-dark weather-desc fs-6 pt-3 m-0"></div>
            <div class="col text-dark weather-degreece fs-4 pt-3 m-0"></div> 
            <div class="col weather-icon text-dark pt-1"></div>    
    </div>
</div>