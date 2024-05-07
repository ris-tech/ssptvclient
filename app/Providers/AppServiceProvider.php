<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Str::macro('lat2cyr', function ($text)
        {
                $cyr = array(
                'ж',  'ч',  'ћ',   'ш',  'ђ',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'j', 'к','љ', 'л', 'м','њ', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц',
                'Ж',  'Ч',  'Ћ',   'Ш',  'Ђ',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'J', 'К','Љ', 'Л', 'М','Њ', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц');
                $lat = array(
                'ž', 'č', 'ć', 'š', 'đ', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k','lj', 'l', 'm','nj', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 
                'Ž', 'Č', 'Ć', 'Š', 'Đ', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K','Lj', 'L', 'M','Nj', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c');
                return str_replace($lat, $cyr, $text);
        });
        Str::macro('datenames', function ($text)
        {
            $datenames = [
                '1' => 'Ponedeljak',
                '2' => 'Utorak',
                '3' => 'Sreda',
                '4' => 'Četvrtak',
                '5' => 'Petak',
                '6' => 'Subota',
                '7' => 'Nedelja'
            ];

            return($datenames[$text]);
        });

    }
}
