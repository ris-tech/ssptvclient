<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\Slide;
use App\Models\SlideImage;
use App\Models\Tv;
use App\Models\Weather;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class getDataFromServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */

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

    public function handle(): void
    {
        $ch = curl_init();

        $optArray = array(
            CURLOPT_URL => env('APP_SERVER_URL').'/api/ssp/'.env('APP_SSP_URL'),
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
        $slideImages = $processed['slideImages'];
        $fbSlides = $processed['fbSlides'];
        $fbPostImages = $processed['fbPostImages'];
        $weather = $processed['weather'];
        $needPull = $processed['needPull'];
        

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

        $addTv = Tv::updateOrInsert(
            ['id' => $tv['id']],
            [
            'id' => $tv['id'],
            'location_id' => $tv['location_id']
        ]);


        //SlideImage::truncate();
        //Slide::truncate();
        $sorting = 0;
        $sortingImages = 0;
        
        foreach($fbSlides as $fbSlide) {
            $getCurrentFbSlide = DB::table('slides')->where('originalId', $fbSlide['post_id'])->first();
            if($getCurrentFbSlide != null) {
                $fbSlideId = $getCurrentFbSlide->id;
                if($getCurrentFbSlide->slide_content != $fbSlide['message']) {
                    DB::table('slides')->where('originalId', $fbSlide['post_id'])->update([
                        'slide_title' => $this->first_sentence($fbSlide['message']),
                        'slide_content' => $this->getContent($fbSlide['message'])
                    ]);
                }                
            } else {
                $fbSlideId = DB::table('slides')->insertGetId([
                    'originalId' => $fbSlide['post_id'],
                    'location_id' => $location['id'],
                    'tv_id' => $tv['id'],
                    'slide_content' => $this->getContent($fbSlide['message']),
                    'slide_title' => $this->first_sentence($fbSlide['message']),
                    'sorting' => 0,
                ]);
                $newSlideData = true;
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
                        $url = $serverURL.'/assets/img/uploads/fb/'.$fbPostImage['attachment']; 
                        $file_name = $fbPostImage['attachment']; 
                        
                        $imagePath = public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads').DIRECTORY_SEPARATOR;
                    
                        if (file_put_contents($imagePath.$file_name, file_get_contents($url))) 
                        { 
                            Log::build([
                                'driver' => 'single',
                                'path' => storage_path('logs/getDataFromServer.log'),
                            ])->info('File downloaded successfully: '.$file_name);
                            $newSlideImageData = false;
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
                    DB::table('slides')->insertGetId(
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
        if($needPull) {
            exec("git pull");
        }
    }
    
}
