<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Tv;
use App\Models\Slide;
use App\Jobs\getDataFromServer;
use App\Models\NewData;
use App\Models\SlideImage;
use App\Models\Weather;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $weather = Weather::where('location_id', $request->locationId)->where('vreme', date('Y-m-d H:00:00'))->first();
        return response()->json($weather);
    }

    public function getUpdatedData(Request $request): JsonResponse
    {
        $updated = NewData::first();
        if($updated == NULL) {
            return response()->json(['status' => 'no']);
        } else {
            DB::table('new_data')->truncate();
            return response()->json(['status' => 'yes']);
        }
    }
    public function view(Request $request)
    {

        $route = $request->path();

        $locationdata = Location::where('name', $route)->first();
   
        if($locationdata == null) {
            getDataFromServer::dispatch();
            return view('viewPanding');
        } else {
            getDataFromServer::dispatch();
            $tvs = Tv::where('location_id', $locationdata->id)->first();
            $locations = Location::where('id', $locationdata->id)->first();

            $slides = Slide::where('tv_id', $tvs->id)->with('slideImages')->get();
            return view('view', ["location" => $locationdata->id, "tv_marquee" => $locationdata->tv_marquee], compact('tvs', 'slides'));
        }

        
    }

    public function NewData() : JsonResponse 
    {
        $ch = curl_init();

        $optArray = array(
            CURLOPT_URL => 'https://muptv.ris-tech.de/api/ssp/'.env('APP_SSP_URL'),
            CURLOPT_RETURNTRANSFER => true
        );

        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        $processed = json_decode($result, true);
        
        $serverURL = $processed['url'];
        $imageURL = $processed['image_url'];
        $tv = $processed['tv'];
        $location = $processed['location'];
        $slides = $processed['slides'];
        $weather = $processed['weather'];
        $newData = false;

        $getLocation = Location::where('name', env('APP_SSP_URL'))->first();
        
        if($getLocation->name != $location['name']) { $newData = true; }
        if($getLocation->street != $location['street']) { $newData = true; }
        if($getLocation->streetno != $location['streetno']) { $newData = true; }
        if($getLocation->postalcode != $location['postalcode']) { $newData = true; }
        if($getLocation->city != $location['city']) { $newData = true; }
        if($getLocation->detail != $location['detail']) { $newData = true; }
        if($getLocation->tv_marquee != $location['tv_marquee']) { $newData = true; }
        if($getLocation->details != $location['details']) { $newData = true; }

        if($newData) {
            $addLocation = Location::updateOrInsert(
                ['id' => $location['id']],
                [
                'id' => $location['id'],
                'name' => $location['name'],
                'street' => $location['street'],
                'streetno' => $location['streetno'],
                'postalcode' => $location['postalcode'],
                'city' => $location['city'],
                'detail' => $location['detail'],
                'tv_marquee' => $location['tv_marquee'],
                'details' => $location['details']
            ]);
        }
        $newSlideData = false;
        $newSlideImageData = false;
        $erasedSlideImagesState = false;

        foreach($slides as $slide) {

            $getSlide = Slide::where('id', $slide['id'])->first();
            $getAllSlideIds[] = $slide['id'];
            if($getSlide != NULL) {

                if($getSlide->slide_content != $slide['slide_content']) { $newSlideData = true; }
                if($getSlide->slide_title != $slide['slide_title']) { $newSlideData = true; }
                if($getSlide->sorting != $slide['sorting']) { $newSlideData = true; }

            }

            if($newSlideData || $getSlide == NULL) {
                $addSlide = Slide::updateOrInsert(
                    ['id' => $slide['id']],
                    [
                    'id' => $slide['id'],
                    'location_id' => $slide['location_id'],
                    'tv_id' => $slide['tv_id'],
                    'slide_content' => $slide['slide_content'],
                    'slide_title' => $slide['slide_title'],
                    'sorting' => $slide['sorting'],
                ]);
            }

            foreach($slide['slide_images'] as $slideImage) {
                $getSlideImage = SlideImage::where('id', $slideImage['id'])->first();

                $getSlideImageIds[$slide['id']][] = $slideImage['id'];

                if($getSlideImage != NULL) {
                    if($getSlideImage->tv_img != $slideImage['tv_img']) { $newSlideImageData = true; }
                    if($getSlideImage->sorting != $slideImage['sorting']) { $newSlideImageData = true; }
                }            

                if($newSlideImageData || $getSlideImage == NULL) {
                    $addSlideImage = SlideImage::updateOrInsert(
                        ['id' => $slideImage['id']],
                        [
                        'id' => $slideImage['id'],
                        'location_id' => $slideImage['location_id'],
                        'tv_id' => $slideImage['tv_id'],
                        'slide_id' => $slideImage['slide_id'],
                        'tv_img' => $slideImage['tv_img'],
                        'sorting' => $slideImage['sorting'],
                    ]);
                
                    $newSlideImageData = true;
                    
                    $url = $imageURL.$slideImage['tv_img']; 
                    $file_name = basename($url); 

                    $imagePath = public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads').DIRECTORY_SEPARATOR;

                    if (file_put_contents($imagePath.$file_name, file_get_contents($url))) 
                    { 
                        Log::build([
                            'driver' => 'single',
                            'path' => storage_path('logs/getDataFromServer.log'),
                        ])->info('ile downloaded successfully: '.$file_name);
                    } 
                    else
                    { 
                        Log::build([
                            'driver' => 'single',
                            'path' => storage_path('logs/getDataFromServer.log'),
                        ])->error('File download Failed');
                    } 
                }

            }
        }
        
        foreach($getSlideImageIds as $slideId => $slideImageId) {
           
            $getSlideImagesNI = SlideImage::whereNotIn('id', $slideImageId)->where('slide_id', $slideId)->get();
            if($getSlideImagesNI->isNotEmpty()) {
                foreach($getSlideImagesNI as $slideImagesNI) {
                    if(file_exists(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$slideImagesNI->tv_img))) {
                        unlink(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$slideImagesNI->tv_img));
                    }
                    SlideImage::where('id', $slideImagesNI->id)->delete();
                }
                $erasedSlideImagesState = true;
            }
        }


        foreach($weather as $weatherData) {
            $addWeather = Weather::updateOrInsert(
                ['id' => $weatherData['id']],
                [
                'id' => $weatherData['id'],
                'location_id' => $weatherData['location_id'],
                'vreme' => $weatherData['vreme'],
                'icon' => $weatherData['icon'],
                'vremetext' => $weatherData['vremetext'],
                'stepeni' => $weatherData['stepeni'],
            ]);
        }

        $erasedSlidesState = false;
        
        $erasedSlides = Slide::whereNotIn('id', $getAllSlideIds)->where('location_id', $location['id'])->get();
        if($erasedSlides->isNotEmpty()) {
            foreach($erasedSlides as $erasedSlide) {
                $imagesToErase = SlideImage::where('slide_id', $erasedSlide->id)->get();
                foreach($imagesToErase as $imageToErase) {
                    if(file_exists(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$imageToErase->tv_img))) {
                        unlink(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$imageToErase->tv_img));
                    }
                    SlideImage::where('id', $imageToErase->id)->delete();
                }
                Slide::where('id', $erasedSlide->id)->delete();
            }
            $erasedSlidesState = true;
        }
        if($newData) { $what = 'newData'; }
        if($newSlideData) { $what = 'newSlideData'; }
        if($newSlideImageData) { $what = 'newSlideImageData'; }
        if($erasedSlidesState) { $what = 'erasedSlidesState'; }
        if($erasedSlideImagesState) { $what = 'erasedSlideImagesState'; }

        if($newData || $newSlideData || $newSlideImageData || $erasedSlidesState || $erasedSlideImagesState) {
            return response()->json(['status' => 'yes', 'what' => $what]);
        } else {
            return response()->json(['status' => 'no']);
        }

    }
}
