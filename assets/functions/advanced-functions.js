/**
 * Created by BIT TECHNOLOGIES on 10/8/2016.
 */

function showMessageAlert(iID, sType, sTitle, sMessage, style)
{
    var sIcon = 'fa-ban';
    if (sType == 'success') sIcon = 'fa-check';

    var replyStatus = $('#'+iID);
    replyStatus.empty();
    //console.log(replyStatus );
    replyStatus.append('<div  class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 alert alert-'+sType+'" style="'+style+'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon fa '+sIcon+'"></i>'+sTitle+'</h4>'+sMessage+'</div>');
}

$(document).on('click', '.panel-heading span.clickable', function(e){chevronClick(this)});

function chevronClick(element)
{
    var $this = $(element);
    var sBodyName = $this.attr("bodyName");
    console.log('formName'+sBodyName);

    if(!$this.hasClass('panel-collapsed'))
        slideUpPanel($this);
    else
        slideDownPanel($this);
}

function slideUpPanel($this)
{
    var sBodyName = $this.attr("bodyName");

    if(!$this.hasClass('panel-collapsed')) {

        if (sBodyName != undefined) {
            $this.parents('.panel').find('#'+sBodyName).slideUp();
        } else {
            $this.parents('.panel').find('.panel-body').slideUp();
        }

        $this.addClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');

    } else {

    }
}

function slideDownPanel($this)
{
    var sBodyName = $this.attr("bodyName");

    if(!$this.hasClass('panel-collapsed')) {


    } else {
        if (sBodyName != undefined) {
            $this.parents('.panel').find('#'+sBodyName).slideDown();
        } else {
            $this.parents('.panel').find('.panel-body').slideDown();
        }

        $this.removeClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
    }
}

function scrollToElement(objectId, iDelayTime)
{
    if (typeof (iDelayTime) === 'undefined') iDelayTime = 1000;

    var target = objectId;
    if (typeof objectId == "string")
    {
        if (objectId[1] == '#') objectId = "#"+objectId;
        var target = $(objectId);
    }

    var targetAnchorOffset=0;
    if (target.hasClass("anchor")) targetAnchorOffset = -50;

    if (target.length > 0) {
        event.preventDefault();
        $('html, body').stop().animate({scrollTop: target.offset().top + targetAnchorOffset }, iDelayTime);
        return true;
    } else
        return false;
}
