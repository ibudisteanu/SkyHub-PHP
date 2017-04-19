/**
 * Created by BIT TECHNOLOGIES on 8/16/2016.
 */
var createToolTipTemplate = function(titleName, titleIcon, type)
{
    return '<div class="tooltip">' +
        '<div class="tooltip-arrow"></div>' +
        '<div class="tooltip-head '+type+'"><h3>' + '<span class="'+titleIcon+'"></span>'+titleName+'</h3></div>' +
        '<div class="tooltip-inner '+type+'"></div>' +
        '</div>'
}

$(document).ready(function(){
    createToolTip( $('[data-toggle="tooltip"]'), "tooltip");
    createToolTip( $('[data-toggle="tooltip-info"]'), "tooltip-info");
    createToolTip( $('[data-toggle="tooltip-success"]'), "tooltip-success");
    createToolTip( $('[data-toggle="tooltip-error"]'), "tooltip-error");
});

var createToolTip = function (object, type, title,  bShowHTML) {
    bShowHTML = typeof bShowHTML !== 'undefined' ? bShowHTML : false;

    switch (type) {
        case 'tooltip':object.tooltip(); break;
        case 'tooltip-info':object.tooltip({template : createToolTipTemplate((typeof title !== 'undefined' ? title : 'Info'),'glyphicon glyphicon-info-sign','tooltip-info'),html:bShowHTML}); break;
        case 'tooltip-success':object.tooltip({template : createToolTipTemplate((typeof title !== 'undefined' ? title : 'Success'),'glyphicon glyphicon-ok-sign','tooltip-success'),html:bShowHTML}); break;
        case 'tooltip-error':object.tooltip({template : createToolTipTemplate((typeof title !== 'undefined' ? title : 'Error'),'glyphicon glyphicon-remove-sign','tooltip-error'),html:bShowHTML}); break;
    }
}

var showToolTip = function(object, text, type, title, position, bShowHTML, bAnimation) {
    type = typeof type !== 'undefined' ? type : 'tooltip-info';
    position = typeof position !== 'undefined' ? position : 'right';
    bShowHTML = typeof bShowHTML !== 'undefined' ? bShowHTML : false;
    bAnimation = typeof  bAnimation !== 'undefined' ? bAnimation : true;

    if ((object.attr("title") != text) && (object.attr("title") != '')) {
        hideToolTip(object);
    }

    object.attr({"data-toggle":type, "data-placement":position,"title":text, "data-animation": (bAnimation == true  ? 'true' : 'false') });
    createToolTip(object, type, title,  bShowHTML);

    var tooltip = object.tooltip('show');
}

var showToolTipTimeOut = function(object, text, type, title, position, bShowHTML, timeOut, bDestroy) {
    timeOut = typeof timeOut !== 'undefined' ? timeOut : 5000;
    bDestroy = typeof bDestroy !== 'undefined' ? bDestroy : false;

    showToolTip(object, text, type, title, position, bShowHTML);
    setTimeout(function () {
        if (bDestroy) object.tooltip('destroy');
    }, timeOut);
}

var hideToolTip = function(object, sAction) {
    sAction = typeof sAction !== 'undefined' ? sAction : 'destroy';
    object.tooltip(sAction);
}


