/**
 * Created by BIT TECHNOLOGIES on 10/8/2016.
 */
var processValidation = function(sId, bStatus, sMessage, sMessageType, sTitle, sPosition, bShowHTML, bHideToolTip, bTimeOut, iTimeOut, bAnimation)
{
    bShowHTML = typeof  bShowHTML  !== 'undefined' ? bShowHTML : false;
    bTimeOut = typeof  bTimeOut !== 'undefined' ? bTimeOut : false;
    iTimeOut = typeof  iTimeOut !== 'undefined' ? iTimeOut : 3000;
    bAnimation = typeof  bAnimation !== 'undefined' ? bAnimation : false;
    sMessageType = typeof sMessageType !== 'undefined' ? sMessageType : "tooltip-error";
    bHideToolTip = typeof bHideToolTip !== 'undefined' ? bHideToolTip : false;

    if (bStatus) {
        if (!bTimeOut) showToolTip($("#" + sId), sMessage, sMessageType , sTitle, sPosition, bShowHTML,bAnimation);
        else showToolTipTimeOut($("#" + sId), sMessage, sMessageType , sTitle, sPosition, bShowHTML,iTimeOut);

        if (bHideToolTip) hideToolTip($("#" + sId),'hide');

        $("#" + sId + "-feedback").attr({'class': "fa fa-times form-control-feedback", 'style': "color:red"});
        return false;
    } else {
        hideToolTip($("#" + sId));
        $("#" + sId + "-feedback").attr({'class': "fa fa-check  form-control-feedback", 'style': "color:green"});
    }
}