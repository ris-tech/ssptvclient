<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function getWeather (Request $request): JsonResponse
    {
        //dd($request);
        $fileId = $request->file;

        $getFile = SlideImage::where('id', $fileId)->first();

        $oldFile = $getFile->tv_img;
        if(file_exists(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$oldFile))) {
            unlink(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$oldFile));
        }
        SlideImage::where('id', $fileId)->delete();

        return response()->json(['success' => 'ok']);
    }
}
