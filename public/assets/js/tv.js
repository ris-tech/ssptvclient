
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': window.csrf_token
    }
});
console.log('Lokacija: '+window.tvLocationId);
let  request = $.ajax({
    url: window.getWeatherData,
    method: 'POST',
    data: {locationId: window.tvLocationId},
    crossDomain: true,
    dataType: 'json',
    success: function(result){   
        $('.weather-desc').html(result.vremetext);  
        $('.weather-degreece').html(result.stepeni+' C°');  
        $('.weather-icon').html('<img src="'+window.weatherIconPath+'/'+result.icon+'">');      
        $('.weather-place').html(window.tvLocationName);
    }
});

setTimeout(() => {
    $('.updateBar').fadeOut();
}, 3000);


function getWeather() {
    let  request = $.ajax({
        url: window.getWeatherData,
        method: 'POST',
        data: {locationId: window.tvLocationId},
        crossDomain: true,
        dataType: 'json',
        success: function(result){   
            $('.weather-desc').html(result.vremetext);  
            $('.weather-degreece').html(result.stepeni+' C°');  
            $('.weather-icon').html('<img src="'+window.weatherIconPath+'/'+result.icon+'">');      
            $('.weather-place').html(window.tvLocationName);
        }
    });
}

setTimeout(() => {
    getWeather();
}, 900000);

function isInternetConnected(){
    if(navigator.onLine) {
        $('.onlinebar').removeClass('bg-danger');
        $('.onlinebar').addClass('bg-success');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.csrf_token
            }
        });
        let req = $.ajax({
            url: window.getNewData,
            method: 'GET',
            data: {},
            crossDomain: true,
            dataType: 'json',
            success: function(result){  
                console.log(result);
                if(result.status == 'yes') {
                    window.location = window.location;
                }
            }
        });
    } else {
        $('.onlinebar').removeClass('bg-success');
        $('.onlinebar').addClass('bg-danger');        
    }
    
    setTimeout(() => {
        isInternetConnected();
    }, 3000);
}

isInternetConnected();


$(document).ready(function () {
    function getTime() {
        var now = new Date(Date.now());
        var formatted =
            (now.getHours() < 10 ? "0" : "") +
            now.getHours() +
            ":" +
            (now.getMinutes() < 10 ? "0" : "") +
            now.getMinutes() +
            ":" +
            (now.getSeconds() < 10 ? "0" : "") +
            now.getSeconds();
        $(".timecont").html(formatted);

        setTimeout(function () {
            getTime();
        }, 1000);
    }
    getTime();

    $(".slide-content").each(function () {
        $(this).summernote({
            height: 300,
        });
    });

    function removeTags(str) {
        if ((str === null) || (str === ''))
            return false;
        else
            str = str.toString();
     
        // Regular expression to identify HTML tags in
        // the input string. Replacing the identified
        // HTML tag with a null string.
        return str.replace(/(<([^>]+)>)/ig, '');
    }


    
    let slides = $('.mySlidesContainer');
    
    let slideImages = [];
    let cntSlides = slides.length;
    let cntSlidesArr = cntSlides-1;
    let crrSlide = 0;
    let currentImage = 0;
    let cntSlideImagesArr = 0;
    let currSlideNo = 99;

    startSlideShow(crrSlide);

    function startImages(slideImage, cntImages, slideNo) {
        if(currSlideNo == slideNo) {
            $(slideImages[slideImage]).fadeIn();
            if(cntImages > 1) {
                setTimeout(function () {
                    if(currentImage == cntSlideImagesArr) {
                        $.when($(slideImages[slideImage]).fadeOut()).then(function() {
                            currentImage = 0;
                            startImages(currentImage, cntImages, slideNo);
                        });
                    } else {
                        $.when($(slideImages[slideImage]).fadeOut()).then(function() {
                            currentImage++; 
                            startImages(currentImage, cntImages, slideNo);
                        });
                    }
                }, 7000);
            }  
        }
        
    }

    function startSlideShow(slideNo) {

        let SlideText = $(slides[slideNo]).find('.mySlidesText-container');
        slideImages = $(slides[slideNo]).find('.slide-images');
        
        let cntSlideImages = slideImages.length;
        
        cntSlideImagesArr = cntSlideImages-1;
        currentImage = 0;
        
        currSlideNo = slideNo;
        startImages(currentImage, cntSlideImages, slideNo);

        let geeks1 = removeTags(SlideText.html());    
        let trim = geeks1.trim();
        let geek = trim.split(" ");
        let timeToRead = (geek.length/238)*60*600;

        
        $(slides[slideNo]).css("display", "flex").hide().fadeIn();
        SlideText.animate({
            scrollTop: 0
        }, 100);

        setTimeout(function () {
                SlideText.animate({
                    scrollTop: SlideText.prop("scrollHeight")
                }, timeToRead);
                setTimeout(function () {
                    if(crrSlide == cntSlidesArr) {
                        
                        $.when($(slides[crrSlide]).fadeOut()).then(function() {
                            
                            crrSlide = 0; 
                            startSlideShow(crrSlide);
                        });
                    } else {
                        $.when($(slides[crrSlide]).fadeOut()).then(function() {
                            SlideText.animate({
                                scrollTop: 0,
                            }, 100);
                            crrSlide++;
                            startSlideShow(crrSlide);
                        });
                    }
                }, timeToRead+5000);
               
        }, 5000);
    }

    $('body').on('click', '.submitCopySlide', function() {
        let newLocationId = $(this).parent().parent().find('select').val();
        let slideId = $(this).parent().parent().find('.slide_id').val();
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.csrf_token
            }
        });
        let  request = $.ajax({
            url: window.copySlideRoute,
            method: 'post',
            data: {locationId: newLocationId, slideId: slideId},
            dataType: 'json',
            success: function(result){
                if(result.success == 'ok') {
                    $('.modal-copy-slide').modal('hide');
                    Swal.fire("Uspešno kopirano!", "", "success");
                }
            }
        });


    });
    $('body').on('click', '.remove-slide', function(event) {
        let slideId = $(this).attr('id');
        Swal.fire({
            title: "Želiš da izbrišes slide?",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Da",
            denyButtonText: `Ne`,
            icon: "question"
        }).then((result) => {
            if (result.isConfirmed) {
                $('body').find('form#'+slideId).submit();
            }
        });
        event.preventDefault();

    });

    $('body').on('click', '.copy-slide', function() {
        let slideId = $(this).attr('id');
        $('.modal-copy-slide').find('.slide_id').val(slideId);
        $('.modal-copy-slide').modal('show');
    });
});
