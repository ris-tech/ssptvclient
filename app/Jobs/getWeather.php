<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Location;
use App\Models\Weather;
use Carbon\Carbon;

class getWeather implements ShouldQueue
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
        $locations = Location::get();
        

        foreach($locations as $location) {
            if(isset($data)) {
                unset($data);
            }
            $getWeather = Weather::where('location_id', $location->id)->where('vreme', date('Y-m-d'))->first();
            if($getWeather == NULL) {
                $ch = curl_init();

                $optArray = array(
                    CURLOPT_URL => 'https://api.weatherapi.com/v1/forecast.json?days=3&key=4822b5df59394b588d7150912242904&q='.$location->name.',Serbia&aqi=no&lang=sr',
                    CURLOPT_RETURNTRANSFER => true
                );

                curl_setopt_array($ch, $optArray);
                $result = curl_exec($ch);
                $processed = json_decode($result, true);
            
                foreach($processed['forecast']['forecastday'] as $forecast) {
                    foreach($forecast['hour'] as $hour) {
                        $stepeni = strval($hour['temp_c']);
                        $data[] = [
                            'location_id' => $location->id,
                            'vreme' => $hour['time'],
                            'vremetext' => $hour['condition']['text'],
                            'stepeni' => $stepeni,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                }
                Weather::insert(
                    $data
                );

                
                if ($processed === false) {
                    echo "Failed loading resource: ";
                    Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/weather.log'),
                    ])->error('Failed load weather API for location '.$location->name.'!');
                } else {
                    Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/weather.log'),
                    ])->info('Weather for location '.$location->name.' is storred');
                }
            } else {
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/weather.log'),
                ])->info('Weather for location '.$location->name.' exists in DB!');
            }
        }
    }
}
