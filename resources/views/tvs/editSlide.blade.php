@extends('layouts.config')

@section('content')
@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif
<h2 class="mt-3 mb-3 border-bottom">{{$slide->location->city}} - Slide {{$slide->sorting+1}}</h2>
<div class="row shadow p-3 mb-3">
    {!! Form::open(['method' => 'POST','route' => ['sc.updateSlide']]) !!}
    <input type="hidden" name="slide_id" value="{{$slide->id}}">
    <div class="col-md-12">
        <div class="col text-end">
            <button type="submit" class="btn btn-success">Sačuvaj</button>
        </div>
    </div>
    <div class="col-md-12">
        <label for="slide-title"><b>Naslov</b></label>
        <input class="form-control" id="slide-title" name="slide_title" type="text" placeholder="Naslovna" value="@if ($slide->slide_title != NULL){{$slide->slide_title}}@endif">
    </div>
    <div class="col-md-12 mt-3">
        <label for="slide-content"><b>Sadržaj</b></label>
        <textarea id="slide-content" class="slide-content form-control @error('slide_content') is-invalid @enderror" name="slide_content" required placeholder="@if ($slide->slide_content == NULL)Ovde dolazi tekst @endif">@if ($slide->slide_content != NULL){{$slide->slide_content}}@endif</textarea>
    </div>
    {!! Form::close() !!}
</div>
<div class="row shadow p-3">
    <div class="col-md-12">
        <div class="col">
            <h2>Galerija</h2>
            <hr />
        </div>
    </div>
    <div class="col-md-12">
        <div class="row img-container">
            @if($slide->slideImages->isEmpty())
                <div class="col-md-2 placeholder-container">
                    <div class="position-relative">
                        <img src="{{asset('assets/img')}}/placeholder.jpg" style="width:100%;">
                    </div>
                </div>
            @else
                @foreach ($slide->slideImages as $slideImage) 
                    <div class="col-md-2 mb-2 slide-image">
                        <div class="position-relative">
                            <div class="position-absolute text-end w-100 p-2"><button type="button" class="btn btn-sm btn-danger del-img" id="{{$slideImage->id}}"><i class="bi bi-trash-fill"></i></button></div>
                            <img src="{{asset('assets/img/uploads')}}/{{$slideImage->tv_img}}" style="width:100%;">
                        </div>
                    </div> 
                @endforeach
            @endif
            <div class="col-md-2 new-file-container">
                <form action="/file-upload" class="dropzone" id="new-slide-image" enctype="multipart/form-data"></form>
            </div>
        </div>
    </div>
</div>
<script>
    Dropzone.options.newSlideImage = { // camelized version of the `id`
        paramName: "new_slide_image", // The name that will be used to transfer the file
        maxFilesize: 2, // MB
        method: "post",
        dictDefaultMessage: 'Izaberi Sliku ili je privuci',
        uploadMultiple: true,
        parallelUploads: 1,
        acceptedFiles: "image/*",
        autoProcessQueue: true,
        url: '{{route("sc.uploadImage")}}',
        headers: {"X-CSRF-TOKEN": "{{csrf_token() }}"},
        init: function () {
            this.on('success', function (file, json) {
                  // creates an array of the files that are in queue
                console.log(json);
                let newimg = '<div class="col-md-2 mb-2 slide-image">' +
                                '<div class="position-relative">' +
                                    '<div class="position-absolute text-end w-100 p-2"><button type="button" class="btn btn-sm btn-danger del-img" id="'+json.fileId+'"><i class="bi bi-trash-fill"></i></button></div>' +
                                    '<img src="{{asset('assets/img/uploads')}}/'+json.file+'" style="width:100%;">' +
                                '</div>' +
                            '</div>';       
                $('.new-file-container').before(newimg);
                $('.placeholder-container').remove();
                this.removeFile(file);
            });
            this.on("sending", function(file, xhr, formData) {
                formData.append("location_id", "{{$slide->location_id}}");
                formData.append("slide_id", "{{$slide->id}}");
                formData.append("tv_id", "{{$slide->tv_id}}");
            });
        }
    };

    let placeholderContainer = '<div class="col-md-2 placeholder-container">' +
                                    '<div class="position-relative">' +
                                        '<img src="{{asset('assets/img')}}/placeholder.jpg" style="width:100%;">' +
                                    '</div>' +
                                '</div>';

    $('body').on('click', '.del-img', function() {
        let delbtn = $(this);
        let imgcontainer = $(this).parent().parent().parent();
        Swal.fire({
            title: "Želiš da izbrišes sliku?",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Da",
            denyButtonText: `Ne`
        }).then((result) => {
            if (result.isConfirmed) {
                $(imgcontainer).remove();            

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token() }}'
                    }
                });
                let  request = $.ajax({
                    url: '{{route("sc.deleteFile")}}',
                    method: 'POST',
                    data: {file: $(delbtn).attr('id')},
                    dataType: 'json',
                    success: function(result){
                        let slideImages = $('body').find('.slide-image');
                        if(slideImages.length == 0) {
                            
                            $('.img-container').prepend(placeholderContainer);
                        }
                        Swal.fire("Izbrisano!", "", "success");

                    }
                });
            }
        });
    });
  </script>
@endsection