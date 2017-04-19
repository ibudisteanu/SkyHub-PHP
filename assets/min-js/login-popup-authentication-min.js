$("#popupAuthenticationModalTrigger").leanModal({
    overlay: 0.6,
    closeButton: ".modal_close"
});

$(function() {
    // Calling Register Form
    $("#btnPopupAuthenticationRegister").click(function() {

        $(".popupLogin").hide();
        $(".popupRegister").show();
        $(".popupTitle").text('Register <?=WEBSITE_TITLE?>');
        calculatePositionsPopupAuthentication(); //Center vertically the popup

        return false;
    });

    // Going back to Social Forms
    $("#btnPopupAuthenticationLogin").click(function() {

        $(".popupRegister").hide();
        $(".popupLogin").show();
        $(".popupTitle").text('Login to <?=WEBSITE_TITLE?>');
        calculatePositionsPopupAuthentication(); //Center vertically the popup

        return false;
    });
});


function openLoginPopupAuthentication()
{
    $("#btnPopupAuthenticationLogin").click();
    $('#popupAuthenticationModalTrigger').click();
    calculatePositionsPopupAuthentication(); //Center vertically the popup

    return false;
}

function openRegistrationPopupAuthentication()
{
    $("#btnPopupAuthenticationRegister").click();
    $('#popupAuthenticationModalTrigger').click();
    calculatePositionsPopupAuthentication(); //Center vertically the popup

    return false;
}

function closePopupAuthentication()
{
    $("#btnPopupAuthenticationModalClose").click();
}

function calculatePositionsPopupAuthentication()
{
    var popupHeight = $("#popupAuthenticationModal").height();
    $("#popupAuthenticationModal").css({'top':'50%','margin-top':'-'+popupHeight/2+'px'});
}