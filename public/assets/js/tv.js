//const { time } = require("console");
function latinToCyrillic(latinStr) {
    var exchanged = "";
    for(var i=0; i<latinStr.length; i++) {
        switch (latinStr.charAt(i)) {
            case "a":   exchanged += "а";    break;
            case "b":   exchanged += "б";    break;
            case "v":   exchanged += "в";    break;
            case "g":   exchanged += "г";    break;
            case "đ":   exchanged += "ђ";    break;
            case "e":   exchanged += "е";    break;
            case "ž":   exchanged += "ж";    break;
            case "z":   exchanged += "з";    break;
            case "i":   exchanged += "и";    break;
            case "j":   exchanged += "ј";    break;
            case "k":   exchanged += "к";    break;
            case "l":
                if(latinStr.charAt(i+1) == "j") {  exchanged += "љ";   i++;  }
                else {  exchanged += "л";   }
                break;
            case "m":   exchanged += "м";    break;
            case "n":
                if(latinStr.charAt(i+1) == "j") {   exchanged += "њ";    i++;   }
                else {  exchanged += "н";   }
                break;
            case "o":   exchanged += "о";    break;
            case "p":   exchanged += "п";    break;
            case "r":   exchanged += "р";    break;
            case "s":   exchanged += "с";    break;
            case "t":   exchanged += "т";    break;
            case "ć":   exchanged += "ћ";    break;
            case "u":   exchanged += "у";    break;
            case "f":   exchanged += "ф";    break;
            case "h":   exchanged += "х";    break;
            case "c":   exchanged += "ц";    break;
            case "č":   exchanged += "ч";    break;
            case "d":
                if(latinStr.charAt(i+1) == "ž") {   exchanged += "џ";   i++; }
                else {   exchanged += "д"; }
                break;
            case "š":   exchanged += "ш";    break;

            case "A":   exchanged += "А";    break;
            case "B":   exchanged += "Б";    break;
            case "V":   exchanged += "В";    break;
            case "G":   exchanged += "Г";    break;
            case "Đ":   exchanged += "Ђ";    break;
            case "E":   exchanged += "Е";    break;
            case "Ž":   exchanged += "Ж";    break;
            case "Z":   exchanged += "З";    break;
            case "I":   exchanged += "И";    break;
            case "J":   exchanged += "Ј";    break;
            case "K":   exchanged += "К";    break;
            case "L":
                if(latinStr.charAt(i+1) == "j") {  exchanged += "Љ";   i++;  }
                else {  exchanged += "Л";   }
                break;
            case "M":   exchanged += "М";    break;
            case "N":
                if(latinStr.charAt(i+1) == "j") {   exchanged += "Њ";    i++;   }
                else {  exchanged += "Н";   }
                break;
            case "O":   exchanged += "О";    break;
            case "P":   exchanged += "П";    break;
            case "R":   exchanged += "Р";    break;
            case "S":   exchanged += "С";    break;
            case "T":   exchanged += "Т";    break;
            case "Ć":   exchanged += "Ћ";    break;
            case "U":   exchanged += "У";    break;
            case "F":   exchanged += "Ф";    break;
            case "H":   exchanged += "Х";    break;
            case "C":   exchanged += "Ц";    break;
            case "Č":   exchanged += "Ч";    break;
            case "D":
                if(latinStr.charAt(i+1) == "ž") {    exchanged += "Џ";   i++;   }
                else {   exchanged += "Д";  }
                break;

            case "Š":   exchanged += "Ш";    break;

            default: exchanged += latinStr.charAt(i);
        }
    }
    return exchanged;
}

$(document).ready(function () {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.csrf_token
        }
    });
    //console.log('Lokacija: '+window.tvLocationId);
    let  request = $.ajax({
        url: window.getWeatherData,
        method: 'POST',
        data: {locationId: window.tvLocationId},
        crossDomain: true,
        dataType: 'json',
        success: function(result){   
            console.log('Weather');
            console.log(result);
            console.log(result.length);
            if(typeof result.length === "undefined") {
                $('.weather-desc').html(latinToCyrillic('Nema podataka'));
            } else {
                $('.weather-desc').html(latinToCyrillic(result.vremetext));  
                $('.weather-degreece').html(result.stepeni+' C°');  
                $('.weather-icon').html('<img src="'+window.weatherIconPath+'/'+result.icon+'">');      
                $('.weather-place').html(latinToCyrillic(window.tvLocationName));
            }
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
        setTimeout(() => {
            getWeather();
        }, 3600000);
    }

    

    getWeather();

    let slides = $('.mySlidesContainer');
    let SlideText = $('body').find('.mySlidesText-container');
    
    let slideImages = [];
    let cntSlides = slides.length;
    let cntSlidesArr = cntSlides-1;
    
    let currentImage = 0;
    let cntSlideImagesArr = 0;
    let currSlideNo = 99;
    let crrSlide = 0;
    
    console.log('slideIds: '+slideIds);
    const slideArr = slideIds.split('|');
    const slideArrCnt = slideArr.length;
    console.log('slideArrCnt: '+slideArrCnt);
    console.log(slideArr);

    function isInternetConnected(){
        if(navigator.onLine) {
            $('.onlinebar').removeClass('bg-danger');
            $('.onlinebar').addClass('bg-success');
        } else {
            $('.onlinebar').removeClass('bg-success');
            $('.onlinebar').addClass('bg-danger'); 
        }
        setTimeout(() => {
            isInternetConnected();
        }, 1000);
    }

    function chkNewData(){
        if(navigator.onLine) {
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
                async: true,
                dataType: 'json',
                success: function(result){  
                    console.log(result);
                    if(result.status == 'yes') {
                        window.location = window.location;
                    }
                }
            });
        } else {
                   
        }
        
        setTimeout(() => {
            chkNewData();
        }, 60000);
    }

    chkNewData();
    isInternetConnected();


    startSlideShow(crrSlide);
    function getTime() {
        var now = new Date(Date.now());
        var formDate = (now.getDate() < 10 ? "0" : "") +
            now.getDate() +
            "." +
            (now.getMonth() < 10 ? "0" : "") +
            (now.getMonth()+1) +
            "." +now.getFullYear();

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
        $(".datecont").html(formDate);

        setTimeout(function () {
            getTime();
        }, 1000);
    }
    getTime();

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

    function nl2br (str, replaceMode, isXhtml) {

        var breakTag = (isXhtml) ? '<br />' : '<br>';
        var replaceStr = (replaceMode) ? '$1'+ breakTag : '$1'+ breakTag +'$2';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
    }

    timeoutId = {};
    let imageFunction = [];
    let oldId = -1;

    function startSlideShow(slideId) {
        let crrSlideId = 0;
        let slideImageTime = 2000;
        let crrSlideImage = 0;
        let crrSlideImageId = 0;
        let slideImages = [];
        
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.csrf_token
            }
        });
        let req = $.ajax({
            url: window.getCurrentSlide,
            method: 'POST',
            data: {slideId: slideArr[slideId]},
            crossDomain: true,
            async: true,
            dataType: 'json',
            success: function(result){ 
                let timeToRead = 5000;
                let slideTitle = result[0].slide_title;
                let slideContent = nl2br(result[0].slide_content);
                $('body').find('.slideTitle').html(slideTitle);
                console.log('slideTitle: '+slideTitle.length);
                $('body').find('.slideContent').html(slideContent);
                slideImages = result[0].slide_images;
                console.log(slideImages);
                let slideImagesCnt = slideImages.length;
                let contentLen = slideContent.length;
                if(contentLen != 0) { 
                    let geeks1 = removeTags(slideContent);    
                    let trim = geeks1.trim();
                    let geek = trim.split(" ");
                    timeToRead = (geek.length/238)*60*600;
                }
                if(timeToRead < 5000) {
                    timeToRead = 2000;  
                }
                console.log('timeToRead: '+timeToRead);
                
                if(slideImagesCnt > 0) {
                    console.log('slideImagesCnt: '+slideImagesCnt);
                    newId = slideId;
                    console.log('newId: '+newId+' oldId: '+oldId);
                    if(oldId != newId) {
                            clearTimeout(timeoutId[oldId]);
                            currImage = 0;
                            startSlideImages(slideImages, slideImagesCnt, newId, true);
                            oldId = newId;
                    }
                               
                } else {
                    clearTimeout(timeoutId[oldId]);
                    oldId = slideId;
                    $('.slide-images').html('<img src="'+imgPath+'/logo_b.png" style="position:absolute;width:100%;height:100%;object-fit:contain;">');
                }
                setTimeout(function () {
                    setTimeout(function () {
                        SlideText.animate({
                            scrollTop: SlideText.prop("scrollHeight")
                        }, timeToRead);
                    });
                    setTimeout(function () {
                        console.log('slideArrCnt in slide: '+slideArrCnt);
                        console.log(slideId);
                        slideId++;
                        let crrSlideId = slideId+1;
                        console.log('crrSlideId: '+crrSlideId);
                        
                        if(slideArrCnt < crrSlideId) {
                            slideId = 0;
                        }
                        startSlideShow(slideId);   
                        
                    }, timeToRead+100);
                }, 100);
            }
        });
    }

    let currImage = 0;

    function startSlideImages(slideImages, slideImagesCnt, slideId, start) {
        console.log('startSlideImages');
        console.log('currImage: '+currImage);
        console.log('slideImagesCnt: '+slideImagesCnt);
        
        if(start) {
            currImage = 0;
            $('.slide-images').html('<img id="'+currImage+'" src="'+imgPath+'/uploads/'+slideImages[currImage].tv_img+'" style="position:absolute;width:100%;height:100%;object-fit:contain;display:none;">');
            $('body').find('img#'+currImage).fadeIn();
            start = false;
            currImage++;
        } else {
            start = false;
            oldImage = currImage-1;
            console.log('oldImage: '+oldImage+' currImage: '+currImage);
            $.when($('body').find('img#'+oldImage).fadeOut()).then(function () {
                $('.slide-images').html('<img id="'+currImage+'" src="'+imgPath+'/uploads/'+slideImages[currImage].tv_img+'" style="position:absolute;width:100%;height:100%;object-fit:contain;display:none;">');
                $('body').find('img#'+currImage).fadeIn();
                currImage++;
            });
        }
        
        if(slideImagesCnt > 1) {
            console.log('slideImagesCnt: '+slideImagesCnt+' currImage: '+currImage);
            if(slideImagesCnt == currImage) {
                currImage = 0;
            }
            timeoutId[slideId] = setTimeout(() => {
                startSlideImages(slideImages, slideImagesCnt, slideId, false);
            }, 2000);
        }

    }
});
