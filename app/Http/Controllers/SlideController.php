<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tv;
use App\Models\Slide;
use App\Models\SlideImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\TvController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function editSlide ($id): View
    {
        $slide = Slide::where('id', $id)->with('slideImages')->with('location')->with('tv')->first();
        $tvs = Tv::where('location_id', $slide->location_id)->with('location')->first();
        //dd($slide);
        return view('tvs.editSlide', compact('slide', 'tvs'));
    }

    public function deleteFile (Request $request): JsonResponse
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

    public function copySlide(Request $request): JsonResponse
    {
        //dd($request);
        $slideId = $request->slideId;
        $locationId = $request->locationId;

        $getOldSlide = Slide::where('id', $slideId)->first();
        $getNewTvId = Tv::where('location_id', $locationId)->first();
        $getLastSorting = Slide::where('tv_id', $getNewTvId->id)->orderBy('sorting', 'desc')->first();
        if($getLastSorting == NULL) {
            $sorting = 0;
        } else {
            $sorting = floatval($getLastSorting->sorting)+1;
        }

        $newSlide = new Slide([
            'location_id' => $locationId,
            'tv_id' => $getNewTvId->id,
            'slide_content' => $getOldSlide->slide_content,
            'slide_title' => $getOldSlide->slide_title,
            'sorting' => $sorting,
            
        ]);
        $newSlide->save();

        $newSlideId = $newSlide->id;

        $getOldImages = SlideImage::where('slide_id', $slideId)->get();

        foreach($getOldImages as $oldImage) {

            $imagePath = public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads').DIRECTORY_SEPARATOR;
            $oldImageFile = $imagePath.$oldImage->tv_img;
            $oldImageFilePathInfo = pathinfo($imagePath.$oldImage->tv_img);


            $extension = $oldImageFilePathInfo['extension'];
            $newImageName = time().'-'.uniqid().'.'.$extension;
            $newImageFile = $imagePath.$newImageName;

            copy($oldImageFile, $newImageFile);

            $slideImage = new SlideImage([
                'location_id' => $locationId,
                'tv_id' => $newSlide->tv_id,
                'slide_id' => $newSlide->id,
                'tv_img' => $newImageName,
                'sorting' => 0
            ]);

            $slideImage->save();
        }


        return response()->json(['success' => 'ok']);
    }

    public function uploadImage (Request $request): JsonResponse
    {
        //dd($request);

        $new_file = str_replace(' ','_',$request->filename);

        $file = $request->file('new_slide_image')[0];
        $extension = $file->getClientOriginalExtension();

        $imageName = time().'-'.uniqid().'.'.$extension;
        $file->move('assets/img/uploads', $imageName);

        $getSorting = SlideImage::where('slide_id', $request->slide_id)->orderBy('sorting', 'desc')->first();

        if($getSorting == NULL) {
            $sorting = 0;
        } else {
            $sorting = floatval($getSorting->sorting)+1;
        }

        $newSlideImage = SlideImage::create([
            'location_id' => $request->location_id,
            'slide_id' => $request->slide_id,
            'tv_id' => $request->tv_id,
            'tv_img' => $imageName,
            'sorting' => $sorting,
        ]);



        return response()->json(['success' => 'ok', 'file' => $imageName, 'fileId' => $newSlideImage->id]);
    }

    public function addSlide (Request $request)
    {
        $getSlides = Slide::where('location_id', $request->location_id)->where('tv_id', $request->tv_id)->orderBy('sorting', 'desc')->first();
        
        if($getSlides != NULL) {
            $sorting = floatval($getSlides->sorting)+1;
        } else {
            $sorting = 0;
        }
        //dd($tv->id);
        $slide = new Slide(
            [
                'location_id' => $request->location_id,
                'tv_id' => $request->tv_id,
                'slide_content' => 'Ovde dolazi tekst',
                'slide_title' => 'Ovde je naslovna',
                'sorting' => $sorting
            ]
        );

        $slide->save();

        return redirect()->route('tvs.show', ['tv' => $request->location_id])->with('success','Slide uspešno kreiran');
        //return view('tvs.show', ["location" => $request->location_id], compact('tv'));
    } 
    
    public function updateSlide(Request $request, Tv $tv): RedirectResponse
    {
        //dd($request);
            
            Slide::where("id", $request->slide_id)->update(
                [
                    'slide_content' => $request->slide_content,
                    'slide_title' => $request->slide_title
                ]
            );
    
        return redirect()->route('sc.editSlide', [$request->slide_id])
                        ->with('success','Slide uspešno ažuriran');
    }

    public function deleteSlide(Request $request): RedirectResponse
    {
        //dd($request->id);

            $getImages = SlideImage::where('slide_id', $request->id)->get();

            if($getImages->isNotEmpty()) {
                foreach($getImages as $Image) {
                    $oldFile = $Image->tv_img;
                    if(file_exists(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$oldFile))) {
                        unlink(public_path('assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$oldFile));
                    }
                    SlideImage::where('id', $Image->id)->delete();
                }
            }
            
            Slide::where("id", $request->id)->delete();
    
        return redirect()->route('tvs.show', [$request->location_id])
                        ->with('success','Slide uspešno izbrisan');
    }
}
