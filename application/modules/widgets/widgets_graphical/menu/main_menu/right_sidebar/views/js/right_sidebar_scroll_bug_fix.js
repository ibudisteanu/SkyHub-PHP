
$.fn.redraw = function(){
    $(this).each(function(){
        var redraw = this.offsetHeight;
    });
};

window.onload = function() {
    // Listen to the double click event.

    $('#rightSideBar').css({"overflow":"hidden"},{"max-height:":"100%"},{"max-height:":"none"},{"padding-left:":"0"},{"padding-right:":"0"});


    var iWidth = $('#rightSideBar').width();
    $('#rightSideBar').width(iWidth + 1);

    setTimeout(function(){
        var iWidth = $('#rightSideBar').width();
        $('#rightSideBar').width(iWidth-1); $(window).trigger('resize');
        $(window).resize(); }, 2);

};