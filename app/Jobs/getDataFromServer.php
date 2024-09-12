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
    public function handle(): void
    {
        $ch = curl_init();

        $optArray = array(
            CURLOPT_URL => 'http://muptv.local/api/ssp/'.env('APP_SSP_URL'),
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
            $addFbSlide = Slide::Insert([
                'location_id' => $location['id'],
                'tv_id' => $tv['id'],
                'slide_content' => $fbSlide['message'],
                'slide_title' => '',
                'sorting' => $sorting,
            ]);
            $sorting++;
            foreach($fbPostImages as $fbPostImage) {
                $addSlideImage = SlideImage::Insert([
                    'location_id' => $location['id'],
                    'tv_id' => $tv['id'],
                    'slide_id' => $addFbSlide->id(),
                    'tv_img' => $fbPostImage['attachment'],
                    'sorting' => $sortingImages,
                ]);
                $sortingImages++;
                $url = $imageURL.'fb/'.$fbPostImage['attachment']; 
                $file_name = basename($url); 

                $imagePath = public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads').DIRECTORY_SEPARATOR;

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

        foreach($slides as $slide) {
            $addSlide = Slide::Insert([
                'location_id' => $slide['location_id'],
                'tv_id' => $slide['tv_id'],
                'slide_content' => $slide['slide_content'],
                'slide_title' => $slide['slide_title'],
                'sorting' => $sorting,
            ]);
            $sorting++;
            foreach($slide['slide_images'] as $slideImage) {
                $addSlideImage = SlideImage::Insert([
                    'id' => $slideImage['id'],
                    'location_id' => $slideImage['location_id'],
                    'tv_id' => $slideImage['tv_id'],
                    'slide_id' => $addSlide->id(),
                    'tv_img' => $slideImage['tv_img'],
                    'sorting' => $sortingImages,
                ]);
                $sortingImages++;
                $url = $imageURL.$slideImage['tv_img']; 
                $file_name = basename($url); 

                $imagePath = public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads').DIRECTORY_SEPARATOR;

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
