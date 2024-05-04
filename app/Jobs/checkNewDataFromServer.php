<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\NewData;
use App\Models\Slide;
use App\Models\SlideImage;
use App\Models\Tv;
use App\Models\Weather;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class checkNewDataFromServer implements ShouldQueue
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

        foreach($slides as $slide) {

            $getSlide = Slide::where('id', $slide['id'])->first();

            if($getSlide != NULL) {

                if($getSlide->slide_content != $slide['slide_content']) { $newSlideData = true; }
                if($getSlide->slide_title != $slide['slide_title']) { $newSlideData = true; }
                if($getSlide->sorting != $slide['sorting']) { $newSlideData = true; }

            }
            if($newSlideData) {
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

                if($getSlideImage->tv_img != $slideImage['tv_img']) { $newSlideImageData = true; }
                if($getSlideImage->sorting != $slideImage['sorting']) { $newSlideImageData = true; }

                if($newSlideImageData) {
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

        if($newData || $newSlideData || $newSlideImageData) {
            NewData::create(['newData' => 'da']);
        }
    }
}
