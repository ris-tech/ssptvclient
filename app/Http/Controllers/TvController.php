<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tv;
use App\Models\Image;
use App\Models\Slide;
use App\Models\SlideImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TvController extends Controller
{
    public function index()
    {
        $locations = Location::get();
        return view('home',compact('locations'));    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show($location)
    {
        
        $tvs = Tv::where('location_id', $location)->with('location')->first();
        $locations = Location::where('id', $location)->first();

        $copyLocations = Location::whereNot('id', $location)->get();

        if ($tvs == NULL) {
             $tvs = new Tv(
                [
                    'location_id' => $location
                ]
            );
            $tvs->save();

            $slides = new Slide(
                [
                    'location_id' => $location,
                    'tv_id' => $tvs->id,
                    'slide_content' => 'Ovde dolazi tekst',
                    'slide_title' => 'Ovde dolazi naslovna',
                    'sorting' => '0'
                ]
            );
            $slides->save();
        } else {
            $slides = Slide::where('tv_id', $tvs->id)->with('slideImages')->get();
        }

        return view('tvs.show', ["location" => $location, "tv_marquee" => $locations->tv_marquee], compact('tvs', 'slides', 'copyLocations'));
    }

    public function addSlide(Request $request)
    {
        $tv = new Tv(
            [
                'location_id' => $request->location_id,
                'tv_marquee' => 'Ovo su slova koja trče',
            ]
        );

        $tv->save();

        $getSlides = Slide::where('location_id', $request->location_id)->orderBy('sorting desc')->first();
        if($getSlides != NULL) {
            $sorting = floatval($getSlides->sorting)+1;
        } else {
            $sorting = 0;
        }

        $slide = new Slide(
            [
                'location_id' => $request->location_id,
                'tv_id' => $tv->id,
                'tv_content' => 'Ovde dolazi tekst',
                'sorting' => $sorting
            ]
        );

        $slideImgage = new SlideImage(
            [
                'location_id' => $request->location_id,
                'tv_id' => $tv->id,
                'slide_id' => $slide->id,
                'tv_img' => 'placeholder',
                'sorting' => '0'
            ]
        );

        return view('tvs.show', ["location" => $request->location_id], compact('tv'));
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Location  $tv
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $tv): View
    {
        return view('tvs.edit',compact('tv'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Location  $tv
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $tv): RedirectResponse
    {
         request()->validate([
            'tv_config' => 'required',
            'tv_img' => 'required',
            'tv_marquee' => 'required',
        ]);
    
        $tv->update($request->all());
    
        return redirect()->route('tvs.index')
                        ->with('success','location updated successfully');
    }
    public function updatetv(Request $request, Tv $tv): RedirectResponse
    {
        //dd($request);
        
        $path = public_path('uploads/');
        !is_dir($path) &&
            mkdir($path, 0777, true);

        $imageName = time().'.'.$request->tv_img->extension(); 

        $request->tv_img->move(public_path('uploads/'), $imageName);


        Tv::where("id", $request->tv_id)->update(
            [
                "tv_config" => $request->tv_config,
                "tv_img" => $imageName,
                "tv_marquee" => $request->tv_marquee,
            ]);
    
        return redirect()->route('tvs.show', [$request->location_id])
                        ->with('success','Tv uspešno unešen');
    }

    public function updateGlobal(Request $request, Location $location): RedirectResponse
    {
        //dd($request);
       
        Location::where("id", $request->location_id)->update(
            [
                "tv_marquee" => $request->tv_marquee,
            ]);
    
        return redirect()->route('tvs.show', [$request->location_id])
                        ->with('success','Tv uspešno unešen');
    }
    
}
