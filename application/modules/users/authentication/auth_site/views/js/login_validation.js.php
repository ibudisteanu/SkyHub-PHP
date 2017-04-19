function validateLoginForm<?=$iLoginNo?>(object) {
    var sId = object.id;
    var sName = object.name; var sValue = '';
    var regex, bError, sMessage, sTitle, sPosition;

    if (object.type=="text") sValue = object.value;
    else  if (object.type=="password") sValue = object.value;

    switch (sName)
    {
        case 'login-username':
            sTitle="Invalid Username";
            sPosition = "top";
            if (sValue.length < 3)
            {
                bError=true;
                sMessage="The username is too small - min 3 chars";
                break;
            }
            if (sValue.length > 20)
            {
                bError=true;
                sMessage="The username is too big - max 20 chars";
                break;
            }
            regex = new RegExp("^[a-zA-Z]+([_ -]?[a-zA-Z0-9])*$");

            bError=!regex.test(sValue);
            sMessage="Only alphanumeric including ._- and space";
            break;
        case 'login-password':
            sTitle="Invalid Password";
            sPosition = "bottom";
            if (sValue.length < 4)
            {
                bError=true;
                sMessage="Your Password is too small - min 4 chars";
                break;
            }
            if (sValue.length > 20)
            {
                bError=true;
                sMessage="Your Password is way too big - max 20 chars";
                break;
            }

            break;
    }

    processValidation(sId,bError,sMessage,'tooltip-error',sTitle,sPosition,true,false,false,3000,true);

    //console.log(sName+' value: '+sValue);
    return true;

}

function validateLoginFormPost<?=$iLoginNo?>()
{
    var bCheck =true;
    bCheck  = validateLoginForm<?=$iLoginNo?>($("#login-username<?=$iLoginNo?>")[0]) && bCheck ;
    bCheck  = validateLoginForm<?=$iLoginNo?>($("#login-password<?=$iLoginNo?>")[0]) && bCheck ;

    if (bCheck)
    {
        sUsername = $("#login-username<?=$iLoginNo?>").val();
        sPassword = $("#login-password<?=$iLoginNo?>").val();

        $.ajax(
            {
                url: document.location.origin+'/api/users/post/authentication/login',
                type: 'post',
                data: { Id: sUsername, Pass: sPassword},
                success: function( outputJSONData )
                {
                    var outputData = $.parseJSON(outputJSONData);

                    if (outputData.result == true){ // successfully
                        hideToolTip($('#login-submitButton<?=$iLoginNo?>'));
                        $("#login-username<?=$iLoginNo?>-feedback").attr({'class': "fa fa-check  form-control-feedback", 'style': "color:green"});
                        $("#login-password<?=$iLoginNo?>-feedback").attr({'class': "fa fa-check  form-control-feedback", 'style': "color:green"});
                        setTimeout(function(){
                            showToolTip($('#login-submitButton<?=$iLoginNo?>'),outputData.message,'tooltip-success','Login successful','top',true);
                            location.reload();
                        }, 5);
                    }
                    else {// wrong id or password
                        $("#login-username<?=$iLoginNo?>-feedback").attr({'class': "fa fa-times form-control-feedback", 'style': "color:red"});
                        $("#login-password<?=$iLoginNo?>-feedback").attr({'class': "fa fa-times form-control-feedback", 'style': "color:red"});
                        showToolTip($('#login-submitButton<?=$iLoginNo?>'),outputData.message,'tooltip-error','Login problem ','bottom',true,'false');
                    }

                    if (outputData.loggedIn)
                        setTimeout(function(){
                            location.reload();
                        },5);
                },
                error: function() {
                    showToolTip($('#login-submitButton<?=$iLoginNo?>'),"An error has encounter submitting your login credentials",'tooltip-error','Login Internal Error','bottom',false,'false');
                }
            });

        return false;
    }

    return bCheck ;
}
