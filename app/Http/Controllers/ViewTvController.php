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
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    public function getSlideIds(Request $request): JsonResponse
    {
        $slides = Slide::get();
        $data = array();
        foreach($slides as $slide) {
            $data[] = $slide->id;
        }
        return response()->json($data);
    }

    public function getCurrentSlide(Request $request): JsonResponse
    {
        $slides = Slide::where('id', $request->slideId)->with('slideImages')->get();

        return response()->json($slides);
    }
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

            $slides = Slide::where('tv_id', $tvs->id)->with('slideImages')->orderBy('fb', 'desc')->orderBy('id', 'asc')->get();
            
            $slideIds = '';
            foreach($slides as $slide) {
                if($slideIds == '') {
                    $slideIds = $slide->id;
                    //$qrCodes = QrCode::size(150)->style('square')->generate($slide->permalink);
                    //dd($qrCodes);
                } else {
                    $slideIds = $slideIds.'|'.$slide->id;
                }
            }

            return view('view', ["location" => $locationdata->id, "tv_marquee" => $locationdata->tv_marquee], compact('tvs', 'slides','slideIds'));
        }

        
    }

    public function getContent($content) {
        $expContent = explode("\n",$content);
        $str = '';
        foreach($expContent as $idx => $lines) {
            if($idx > 1) {
                $str = $str.'<br>'.$lines;
            } elseif($idx == 1) {
                $str = $lines;
            }
        }
        return $str;
       
    }

    public function first_sentence($content) {
        $expContent = explode("\n",$content);
        
        return $expContent[0];
       
    }

    public function NewData() : JsonResponse 
    {

        /*SlideImage::where('id', '>', 0)->delete();
        Slide::where('id', '>', 0)->delete();*/

        $ch = curl_init();

        $optArray = array(
            CURLOPT_URL => config('ssptvconfig.APP_SERVER_URL').'/api/ssp/'.config('ssptvconfig.APP_SSP_URL'),
            CURLOPT_RETURNTRANSFER => true
        );        
        
        curl_setopt_array($ch, $optArray); 
        
        $result = curl_exec($ch);
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if(!$result)
        {
            return response()->json(['status' => 'exec failed']);
        }
      
        if($httpcode < 400)
        {
            $processed = json_decode($result, true);
        
            $serverURL = $processed['url'];
            $imageURL = $processed['image_url'];
            $needPull = $processed['needPull'];
            $tv = $processed['tv'];
            $location = $processed['location'];
            $slides = $processed['slides'];
            $slideImages = $processed['slideImages'];
            $fbSlides = $processed['fbSlides'];
            $fbPostImages = $processed['fbPostImages'];
            $weather = $processed['weather'];
            $newData = false;
            if($needPull) {
                exec('cd /var/www/html/ssptvclient && git pull 2>&1', $output);
                
                if($output[0] != 'Already up to date.') {
                    return response()->json(['status' => 'yes', 'what' => 'git pull']);
                }
            }

            //dd(env('APP_SSP_URL'));
            $getLocation = Location::where('name', config('ssptvconfig.APP_SSP_URL'))->first();
            

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
                    'tv_marquee' => $location['tv_marquee']
                ]);
            }
            $newFbSlideData = false;
            $newSlideData = false;
            $newSlideImageData = false;
            $erasedSlideImagesState = false;
            $erasedSlidesState = false;
            $erasedSlidesMsg = '';

            $sorting = 0;
            $sortingImages = 0;

            $ids = [];

            foreach($fbSlides as $fbSlide) {
                    $ids[] = $fbSlide['post_id'];
            }

            foreach($slides as $slide) {
                $ids[] = $slide['id'];
            }
            
            $slidesNotInDB = Slide::whereNotIn('originalId', $ids)->get();

            if($slidesNotInDB->isNotEmpty()) {
                foreach($slidesNotInDB as $slide) {
                    
                    $slide_images = SlideImage::where('slide_id', $slide->id)->get();
                
                    foreach($slide_images as $slide_image) {
                        Storage::disk('images')->delete($slide_image->tv_img);
                        SlideImage::find($slide_image->id)->delete();
                    }
                    Slide::where('id', $slide->id)->delete();
                    $erasedSlidesState = true;
                    $erasedSlidesMsg = $slide->id;
                }
            }


            foreach($fbSlides as $fbSlide) {


                $getCurrentFbSlide = DB::table('slides')->where('originalId', $fbSlide['post_id'])->first();
                if($getCurrentFbSlide != null) {
                    $fbSlideId = $getCurrentFbSlide->id;
                    if($getCurrentFbSlide->slide_content != $this->getContent($fbSlide['message'])) {
                        DB::table('slides')->where('originalId', $fbSlide['post_id'])->update([
                            'slide_title' => $this->first_sentence($fbSlide['message']),
                            'slide_content' => $this->getContent($fbSlide['message'])
                        ]);
                        $newFbSlideData = true;
                    }                
                } else {
                    $cntSlides = Slide::where('fb', true)->count();
                    if($cntSlides == 5) {
                        $getFirstSlide = Slide::where('fb', true)->first();
                        $getFirstSlideImages = SlideImage::where('slide_id', $getFirstSlide->id)->get();
                        foreach($getFirstSlideImages as $images) {
                            Storage::disk('images')->delete($images->tv_img);
                            SlideImage::where('id', $images->id)->delete();
                        }
                        Slide::where('id', $getFirstSlide->id)->delete();
                        $fbSlideId = DB::table('slides')->insertGetId([
                            'originalId' => $fbSlide['post_id'],
                            'permalink' => $fbSlide['permalink'],
                            'location_id' => $location['id'],
                            'tv_id' => $tv['id'],
                            'slide_content' => $this->getContent($fbSlide['message']),
                            'slide_title' => $this->first_sentence($fbSlide['message']),
                            'fb' => true,
                            'sorting' => 0,
                        ]);
                        $newFbSlideData = true;
                    } elseif($cntSlides < 5) {
                        $fbSlideId = DB::table('slides')->insertGetId([
                            'originalId' => $fbSlide['post_id'],
                            'permalink' => $fbSlide['permalink'],
                            'location_id' => $location['id'],
                            'tv_id' => $tv['id'],
                            'slide_content' => $this->getContent($fbSlide['message']),
                            'slide_title' => $this->first_sentence($fbSlide['message']),
                            'fb' => true,
                            'sorting' => 0,
                        ]);
                        $newFbSlideData = true;
                    }
                }
                
                

                foreach($fbPostImages as $fbPostImage) {
                    if($fbPostImage['post_id'] == $fbSlide['post_id']) {
                        $getCurrentFbSlideImage = DB::table('slide_images')->where('tv_img', $fbPostImage['attachment'])->first();
                        if($getCurrentFbSlideImage == NULL) {
                            SlideImage::Insert([
                                'location_id' => $location['id'],
                                'tv_id' => $tv['id'],
                                'slide_id' => $fbSlideId,
                                'tv_img' => $fbPostImage['attachment'],
                                'sorting' => $sortingImages,
                            ]);
                            $sortingImages++;
                            $url = $serverURL.'/images/uploads/fb/'.$fbPostImage['attachment']; 
                            $file_name = $fbPostImage['attachment']; 
                            
                            $imagePath = public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads').DIRECTORY_SEPARATOR;
                            $newSlideImageData = true;
                            if (file_put_contents($imagePath.$file_name, file_get_contents($url))) 
                            { 
                                Log::build([
                                    'driver' => 'single',
                                    'path' => storage_path('logs/getDataFromServer.log'),
                                ])->info('File downloaded successfully: '.$file_name);
                                
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
                
            }

            
            foreach($slides as $slide) {
                $getCurrentSlide = DB::table('slides')->where('originalId', $slide['id'])->first();
            
                if($getCurrentSlide != null) {
                    $slideId = $getCurrentSlide->id;
                    if($getCurrentSlide->slide_content != $slide['slide_content']) {                    
                        DB::table('slides')->where('originalId', $slide['id'])->update(
                        [
                            'slide_content' => $slide['slide_content'],
                            'slide_title' => $slide['slide_title']
                        ]);
                    }
                } else {
                    $slideId = DB::table('slides')->insertGetId(
                        [
                        'originalId' => $slide['id'],
                        'location_id' => $slide['location_id'],
                        'tv_id' => $slide['tv_id'],
                        'slide_content' => $slide['slide_content'],
                        'slide_title' => $slide['slide_title'],
                        'sorting' => $sorting,
                    ]);
                    $newSlideData = true;
                    $sorting++;
                }

                foreach($slideImages as $slideImage) {
                    if($slideImage['slide_id'] == $slide['id']) {
                        $getCurrentSlideImage = DB::table('slide_images')->where('tv_img', $slideImage['tv_img'])->first();
                        if($getCurrentSlideImage == NULL) {
                            $addSlideImage = DB::table('slide_images')->insertGetId([
                                'location_id' => $slideImage['location_id'],
                                'tv_id' => $slideImage['tv_id'],
                                'slide_id' => $slideId,
                                'tv_img' => $slideImage['tv_img'],
                                'sorting' => $sortingImages,
                            ]);
                            $sortingImages++;
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

            
            
            
            if($newData) { $what = 'newData'; }
            if($newSlideData) { $what = 'newSlideData'; }
            if($newFbSlideData) { $what = 'newFbSlideData'; }
            if($newSlideImageData) { $what = 'newSlideImageData'; }
            if($erasedSlidesState) { $what = 'erasedSlidesState'; }
            if($erasedSlideImagesState) { $what = 'erasedSlideImagesState'; }

            if($erasedSlidesState) {
                
                return response()->json(['status' => 'yes', 'what' => $erasedSlidesMsg]);
            }


            if($newData || $newSlideData || $newFbSlideData || $newSlideImageData || $erasedSlideImagesState) {
                return response()->json(['status' => 'yes', 'what' => $what]);
            } else {
                return response()->json(['status' => 'no']);
            }
        } else {    
            return response()->json(['status' => 'no Response']);
        }

    }
}
