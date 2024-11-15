//const { time } = require("console");
function latinToCyrillic(latinStr) {
    var ss=["B","b","V","v","G","g","D","d","Đ","đ","Ž","ž","Z","z","I","i","L","l","LJ","Lj","lj","N","n","NJ","Nj","nj","P","p","R","r","S","s","Ć","ć","Č","č","U","u","F","f","C","c","Š","š"];
    var cyr=["Б","б","В","в","Г","г","Д","д","Ђ","ђ","Ж","ж","З","з","И","и","Л","л","Љ","Љ","љ","Н","н","Њ","Њ","њ","П","п","Р","р","С","с","Ћ","ћ","Ч","ч","У","у","Ф","ф","Ц","ц","Ш","ш"];
    for(var i=0;i<ss.length;i++) {
        var tt=cyr[i];
        latinStr=latinStr.replace(new RegExp(ss[i], "g"),tt);
    }
    return latinStr;
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
            //console.log('Weather');
            //console.log(result);
            //console.log(result.length);
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
                $('.weather-desc').html(latinToCyrillic(result.vremetext));  
                $('.weather-degreece').html(result.stepeni+' C°');  
                $('.weather-icon').html('<img src="'+window.weatherIconPath+'/'+result.icon+'">');      
                $('.weather-place').html(latinToCyrillic(window.tvLocationName));
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
    
    //console.log('slideIds: '+slideIds);
    const slideArr = slideIds.split('|');
    const slideArrCnt = slideArr.length;
    //console.log('slideArrCnt: '+slideArrCnt);
    //console.log(slideArr);

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
                //console.log(result);
                let timeToRead = 5000;
                let slideTitle = result[0].slide_title;
                let slideContent = nl2br(result[0].slide_content);
                let permalink = result[0].permalink;
                $('body').find('.qrcode').html('');
                if(permalink != null) {                
                    new QRCode("qrcode", {
                        text: permalink,
                        width: 150,
                        height: 150,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H,
                    });
                }
                //$('body').find('.qrcode').html(qrcode);
                $('body').find('.slideTitle').html(slideTitle);
                //console.log('slideTitle: '+slideTitle.length);
                $('body').find('.slideContent').html(slideContent);
                slideImages = result[0].slide_images;
                //console.log(slideImages);
                let slideImagesCnt = slideImages.length;
                let contentLen = slideContent.length;
                if(contentLen != 0) { 
                    let geeks1 = removeTags(slideContent);    
                    let trim = geeks1.trim();
                    let geek = trim.split(" ");
                    timeToRead = (geek.length/238)*60*600;
                }
                if(timeToRead < 5000) {
                    timeToRead = 5000;  
                }
                //console.log('timeToRead: '+timeToRead);
                
                if(slideImagesCnt > 0) {
                    //console.log('slideImagesCnt: '+slideImagesCnt);
                    newId = slideId;
                    //console.log('newId: '+newId+' oldId: '+oldId);
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
                        //console.log('slideArrCnt in slide: '+slideArrCnt);
                        //console.log(slideId);
                        slideId++;
                        let crrSlideId = slideId+1;
                        //console.log('crrSlideId: '+crrSlideId);
                        
                        if(slideArrCnt < crrSlideId) {
                            slideId = 0;
                        }
                        startSlideShow(slideId);   
                        
                    }, timeToRead+5000);
                }, 5000);
            }
        });
    }

    let currImage = 0;

    function startSlideImages(slideImages, slideImagesCnt, slideId, start) {
        //console.log('startSlideImages');
        //console.log('currImage: '+currImage);
        //console.log('slideImagesCnt: '+slideImagesCnt);
        
        if(start) {
            currImage = 0;
            $('.slide-images').html('<img id="'+currImage+'" src="'+imgPath+'/uploads/'+slideImages[currImage].tv_img+'" style="position:absolute;width:100%;height:100%;object-fit:contain;display:none;">');
            $('body').find('img#'+currImage).fadeIn();
            start = false;
            currImage++;
        } else {
            start = false;
            oldImage = currImage-1;
            //console.log('oldImage: '+oldImage+' currImage: '+currImage);
            $.when($('body').find('img#'+oldImage).fadeOut()).then(function () {
                $('.slide-images').html('<img id="'+currImage+'" src="'+imgPath+'/uploads/'+slideImages[currImage].tv_img+'" style="position:absolute;width:100%;height:100%;object-fit:contain;display:none;">');
                $('body').find('img#'+currImage).fadeIn();
                currImage++;
            });
        }
        
        if(slideImagesCnt > 1) {
            //console.log('slideImagesCnt: '+slideImagesCnt+' currImage: '+currImage);
            if(slideImagesCnt == currImage) {
                currImage = 0;
            }
            timeoutId[slideId] = setTimeout(() => {
                startSlideImages(slideImages, slideImagesCnt, slideId, false);
            }, 7000);
        }

    }
});
