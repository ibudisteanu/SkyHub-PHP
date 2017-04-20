function validateRegistrationForm(object, iRegistrationNo, bTimeOut, bHideToolTip) {
    bTimeOut = typeof bTimeOut !== 'undefined' ? bTimeOut : false;
    bHideToolTip = typeof bHideToolTip !== 'undefined' ? bHideToolTip : false;

    var sId = object.id;
    var sName = object.name; var sValue = '';
    var regex, bError, sMessage, sTitle, sPosition;

    if (object.type=="text") sValue = object.value;
    else  if (object.type=="password") sValue = object.value;

    switch (sName)
    {
        case 'register-username':
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
            if (!bError)
            {
                $.ajax(
                    {
                        url: document.location.origin+'/api/users/post/registration/check-used-username',
                        type: 'post',
                        data: { Username: sValue},
                        success: function( outputJSONData )
                        {
                            var outputData = $.parseJSON(outputJSONData);

                            if (outputData.result == false){ // successfully - valid email
                                hideToolTip($("#" + sId));
                                //processValidation(sId,true,outputData.message,'tooltip-success',"Valid Username","top",true,false,false,3000,true);
                            }
                            else {//email already used
                                if (outputData.rejected == true){
                                    //processValidation(sId,true,outputData.message,'tooltip-info',"Couldn't validate it","top",true,false,false,3000,true);
                                } else
                                    processValidation(sId,true,outputData.message,'tooltip-error',"Username already Used","top",true,false,false,3000,true);
                            }
                        },
                        error: function() {
                            processValidation(sId,true,"An error has encounter checking your username for duplication",'tooltip-error',"Username Internal Error","top",true,false,false,3000,true);
                        }
                    });
            }
            break;
        case 'register-email':
            regex = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;

            bError=!regex.test(sValue);
            sMessage="Invalid email address";
            sTitle="Invalid Email";
            sPosition = "top";
            if (!bError)
            {
                $.ajax(
                    {
                        url: document.location.origin+'/api/users/post/registration/check-used-email',
                        type: 'post',
                        data: { Email: sValue},
                        success: function( outputJSONData )
                        {
                            var outputData = $.parseJSON(outputJSONData);

                            if (outputData.result == false){ // successfully - valid email
                                hideToolTip($("#" + sId));
                                //processValidation(sId,true,outputData.message,'tooltip-success',"Valid Email","top",true,false,false,3000,true);
                            }
                            else {//email already used
                                if (outputData.rejected == true){
                                    //processValidation(sId,true,outputData.message,'tooltip-info',"Couldn't validate it","top",true,false,false,3000,true);
                                } else
                                    processValidation(sId,true,outputData.message,'tooltip-error',"Email already Used","top",true,false,false,3000,true);
                            }
                        },
                        error: function() {
                            //processValidation(sId,true,"An error has encounter checking your email address for duplication",'tooltip-error',"Email Internal Error","top",true,false,false,3000,true);
                        }
                    });
            }
            break;
        case 'register-firstName':
            sTitle="Invalid First Name";
            sPosition = "top";
            if (sValue.length < 2)
            {
                bError=true;
                sMessage="Your First Name is too small - min 3 chars";
                break;
            }
            if (sValue.length > 20)
            {
                bError=true;
                sMessage="Your First Name is way too big - max 20 chars";
                break;
            }
            regex = new RegExp("^([^0-9]*)$");
            bError=!regex.test(sValue);
            sMessage="Digits are not allowed in your First Name";
            break;
        case 'register-lastName':
            sTitle="Invalid Last Name";
            sPosition = "top";
            if (sValue.length < 2)
            {
                bError=true;
                sMessage="Your Last Name is too small - min 3 chars";
                break;
            }
            if (sValue.length > 20)
            {
                bError=true;
                sMessage="Your Last Name is way too big - max 20 chars";
                break;
            }
            regex = new RegExp("^([^0-9]*)$");
            bError=!regex.test(sValue);
            sMessage="Digits are not allowed in your Last Name";
            break;
        case 'register-city':
            sTitle = "Invalid City";
            sPosition = "bottom";
            if (sValue.length < 2)
            {
                bError=true;
                sMessage="Your City is too small - min 3 chars";
                break;
            }
            if (sValue.length > 20)
            {
                bError=true;
                sMessage="Your City is way too big - max 20 chars";
                break;
            }
            regex = new RegExp("^([^0-9]*)$");
            bError=!regex.test(sValue);
            sMessage="Digits are not allowed in your City";
            break;
        case 'register-country':
            sTitle = "Invalid Country";
            sPosition = "bottom";
            regex = new RegExp("^([^0-9]*)$");
            bError=!regex.test(sValue);
            sMessage="Digits are not allowed in your Country";
            break;
        case 'register-password':
        case 'register-passwordConfirmation':
            sTitle="Invalid Password";
            sPosition = "top";
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

            object = $("#register-passwordConfirmation"+iRegistrationNo);

            sId = $("#register-password"+iRegistrationNo).attr("id");
            hideToolTip($("#" + sId));
            $("#" + sId + "-feedback").attr({'class': "fa fa-check  form-control-feedback", 'style': "color:green"});

            sId = $("#register-passwordConfirmation"+iRegistrationNo).attr("id");
            if ($("#register-password"+iRegistrationNo).val() != $("#register-passwordConfirmation"+iRegistrationNo).val())
            {
                sTitle="Invalid Passwords";
                bError=true;
                sMessage="The password don't match";
                break;
            } else
                bError=false;

            break;
    }

    processValidation(sId,bError,sMessage,'tooltip-error',sTitle,sPosition,true,bHideToolTip,bTimeOut,3000,true);

    return !bError;

}

function validateRegistrationFormPost(object, iRegistrationNo)
{
    var bCheck=true;
    bCheck = validateRegistrationForm($("#register-username"+iRegistrationNo)[0], iRegistrationNo ,true, !bCheck) && bCheck;
    console.log(bCheck);
    bCheck = validateRegistrationForm($("#register-email"+iRegistrationNo)[0], iRegistrationNo , true, !bCheck) && bCheck;
    console.log(bCheck);
    bCheck = validateRegistrationForm($("#register-firstName"+iRegistrationNo)[0], iRegistrationNo ,true, !bCheck) && bCheck;
    console.log(bCheck);
    bCheck = validateRegistrationForm($("#register-lastName"+iRegistrationNo)[0], iRegistrationNo, true, !bCheck) && bCheck;
    console.log(bCheck);
    bCheck = validateRegistrationForm($("#register-password"+iRegistrationNo)[0], iRegistrationNo, true, !bCheck) && bCheck;
    console.log(bCheck);
    bCheck = validateRegistrationForm($("#register-passwordConfirmation"+iRegistrationNo)[0], iRegistrationNo, true, !bCheck) && bCheck;
    console.log(bCheck);
    bCheck = validateRegistrationForm($("#register-city"+iRegistrationNo)[0], iRegistrationNo, true, !bCheck) && bCheck;
    console.log(bCheck);
    bCheck = validateRegistrationForm($("#register-countrySelectorCode"+iRegistrationNo)[0], iRegistrationNo, true, !bCheck) && bCheck;

    console.log(bCheck);

    return bCheck;
}

