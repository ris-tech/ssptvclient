<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Tv;
use App\Models\Slide;
use App\Jobs\getDataFromServer;
use App\Models\Weather;
use Illuminate\Http\JsonResponse;

class ViewTvController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWeatherData(Request $request): JsonResponse
    {
        $
        $weather = Weather::where('location_id', $request->locationId)->where('vreme', date('Y-m-d H:00:00'))->first();
        return response()->json($weather);
    }
    public function view(Request $request)
    {

        $route = $request->path();

        $locationdata = Location::where('name', $route)->first();
   
        if($locationdata == null) {
            getDataFromServer::dispatch();
            return view('viewPanding');
        } else {
            $tvs = Tv::where('location_id', $locationdata->id)->first();
            $locations = Location::where('id', $locationdata->id)->first();

            $slides = Slide::where('tv_id', $tvs->id)->with('slideImages')->get();
            return view('view', ["location" => $locationdata->id, "tv_marquee" => $locationdata->tv_marquee], compact('tvs', 'slides'));
        }

        
    }
}
