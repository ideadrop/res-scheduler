
function iddMessage(options){
    if(options.selector == undefined){
        console.log("%c" + 'undefined selector', "color:red;font-weight:bold;");
        return false;
    }
    
    $('.rss-alert').remove();

    var insertType = (options.insertType == undefined)?'':options.insertType;
    var message = (options.message == undefined)?'undefined':options.message;
    var status = (options.status == undefined)?'success':options.status;
    var selector = (options.selector == undefined)?'undefined':options.selector;
    var autoCloseTime = (options.autoCloseTime == undefined)?'':options.autoCloseTime;

    var dynamicClass = 'alert-'+(Math.floor(Date.now() / 1000));
    var html = '';
    html+='<div class="rss-alert '+dynamicClass+' alert alert-'+status+'">';
    html+='<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>';
    html+= message;
    html+='</div>';
    switch (insertType){
        case 'append' :$(selector).append(html);break;
        case 'prepend' :$(selector).prepend(html);break;
        case 'html' :$(selector).html(html);break;
        case 'after' :$(selector).after(html);break;
        case 'before' :$(selector).before(html);break;
        default :   $(selector).before(html);break;
    }
    if(autoCloseTime!='') {
        setTimeout(function () {
            $('.' + dynamicClass).remove();
        }, autoCloseTime);
    }
}
var delayFunction = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function cl(data){
    console.log(data);
}

function scrollToTop() {
    $('html,body').animate({
        scrollTop: ($('html').offset().top) - 0
    }, 0);
}
function iddScrollTo(options) {
    var selector = (options.selector == undefined) ? '' : options.selector;
    if (selector == '') {
        console.log('No selector for scroll');
        return false;
    }
    if (selector.length == 0) {
        console.log('Scroll selector element is not found on DOM');
        return false;
    }
    var fromTop = (options.fromTop == undefined) ? 60 : options.fromTop;
    if (fromTop != 0 && $('.billing-expiry-msg').length > 0) {
        fromTop += 46;
    }
    var delay = (options.delay == undefined) ? 1000 : options.delay;

    $('html,body').animate({
        scrollTop: (selector.offset().top) - fromTop
    }, delay);
}
function commaToArray(str) {
    if (str == '' || str == undefined) {
        return [];
    }
    str = str.split(",");
    return str;
}
function arrayToComma(arr) {
    if (arr == '' || arr == undefined) {
        return '';
    }
    arr = arr.toString();
    return arr;
}
function addToArray(val, arr) {
    if ($.inArray(val, arr) <= -1) {
        if (val != '') {
            arr.push(val);
        }
    }
    return arr;
}
function removeFromArray(search, arr) {
    arr = jQuery.grep(arr, function (a) {
        return a !== search;
    });
    return arr;
}
function redirectTo(url) {
    window.location = url;
}
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
