<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Weather;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $url = env('APP_URL');
        $image_url = $url.'/assets/img/uploads/';
        $slides = Slide::where('location_id', $id)->with('slideImages')->get();
        $weather = Weather::where('location_id', $id)->where('vreme', '>=', date('Y-m-d'))->get();

        return response()->json([
            'url' => $url,
            'image_url' => $image_url,
            'needPull' => false,
            'slides: ' => $slides,
            'weather' => $weather,
            
        ], 200);
        //update
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
