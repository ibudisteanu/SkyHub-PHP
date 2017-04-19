
function initializeAddForumTopicForm(sParentId, sFormIndex, sLanguage)
{
    if (typeof sLanguage == 'undefined') sLanguage = '';

    $('#addForumTopicBodyCode_'+sParentId+'_'+sFormIndex).summernote({ height: 200,  minHeight: null,  maxHeight: null,  focus: true, styleWithSpan: false });
    $('#addForumTopicImageUpload_'+sParentId+'_'+sFormIndex).filestyle({buttonName: 'btn-success', buttonText: ' Upload Image', iconName: 'glyphicon glyphicon-folder-open'});
    $('#addForumTopicCoverImageUpload_'+sParentId+'_'+sFormIndex).filestyle({ buttonName: 'btn-success',  buttonText: ' Upload Cover',  iconName: 'glyphicon glyphicon-folder-open' });

    titleKeyDown($("#addForumTopicTitle_"+sParentId+"_"+sFormIndex), sParentId, sFormIndex);

    countrySelectObject = {};
    if ((sLanguage != '')) {
        countrySelectObject .defaultCountry = sLanguage;
        countrySelectObject.preferredCountries = ['ca', 'gb', 'us', sLanguage ];
    } else
        countrySelectObject.preferredCountries = ['ca', 'gb', 'us' ];

    $("#addForumTopicCountry_"+sParentId+"_"+sFormIndex).countrySelect(countrySelectObject);

}

function addForumTopicSubmission (sActionName, sParentId, sTopicId, sFormIndex, sFormResponseType)
{
    //var sParentId = $('#addForumTopicParentId')[0].value;

    console.log('addForumTopic merge');
    console.log(sActionName );

    var arrData = {
        'addForumTopic-ParentId': sParentId,
        'addForumTopic-Id' : sTopicId,
        'addForumTopic-title': $('#addForumTopicTitle_'+sParentId+'_'+sFormIndex)[0].value,
        'addForumTopic-inputKeywords': $('#addForumTopicInputKeywords_'+sParentId+'_'+sFormIndex)[0].value,
        'addForumTopic-image': $('#addForumTopicImage_'+sParentId+'_'+sFormIndex)[0].value,
        'addForumTopic-coverImage': $('#addForumTopicCoverImage_'+sParentId+'_'+sFormIndex)[0].value,
        'addForumTopic-bodyCode': $('#addForumTopicBodyCode_'+sParentId+'_'+sFormIndex)[0].value,
        'addForumTopic-country': $('#addForumTopicCountry_'+sParentId+'_'+sFormIndex).countrySelect("getSelectedCountryData").iso2,
        'addForumTopic-city': $('#addForumTopicCity_'+sParentId+'_'+sFormIndex)[0].value,
        'addForumTopic-sFormResponseType' : sFormResponseType,
    } ;
    arrData[sActionName] = 'true';

    /*$topicId = $('#addForumTopicId_'+sParentId+'_'+sFormIndex);
     if ($topicId[0] != undefined) arrData['addForumTopic-Id'] = $topicId[0].value;*/

    $topicShortDescription = $('#addForumTopicShortDescription_'+sParentId+'_'+sFormIndex);
    if ($topicShortDescription[0] != undefined) arrData['addForumTopic-shortDescription'] = $topicShortDescription[0].value;

    $topicImportance = $('#addForumTopicImportance_'+sParentId+'_'+sFormIndex);
    if ($topicImportance[0] != undefined) arrData['addForumTopic-importance'] = $topicImportance[0].value;

    $topicUrlName = $('#addForumTopicUrlName_'+sParentId+'_'+sFormIndex);
    if ($topicUrlName[0] != undefined) arrData['addForumTopic-urlName'] = $topicUrlName[0].value ;

    showAddTopicSubmissionDiv(false,sParentId, sFormIndex);

    $.ajax({
        type: 'POST',
        url: document.location.origin+'/api/topic/post/add/'+sParentId+'/false',
        data: arrData,
        success: function( data ) {
            showAddTopicSubmissionDiv(true, sParentId, sFormIndex);

            console.log(data);
            var data = $.parseJSON(data);

            if (data.result == false) {
                showMessageAlert('addForumTopicAlertsContainer_'+sParentId+'_'+sFormIndex, 'danger', 'Problems publishing the topic', data.message);

                if ((data.logged == false))
                    openLoginPopupAuthentication();
            }
            else
            if (data.result == true)
            {
                /*var $addTopicForm = $('#addTopicFormContainer_'+sParentId+'_'+sFormIndex);
                 $addTopicForm.hide();//hiding the form*/

                if (data.message.length > 0)//showing the new topic in the FEED is not done yet
                    showMessageAlert('addForumTopicAlertsContainer_'+sParentId+'_'+sFormIndex, 'success', 'Successful publishing the topic', data.message);

                if (data.BlockHTMLCode.length > 0)
                {
                    //console.log(repliesNewContainer);
                    console.log('sActionName '+sActionName );
                    if (sActionName == "addForumTopic") {

                        sTopicId = data.sNewTopicId;
                        var $topicsContainer = $('#topicsContainer_' + sParentId + '_' + sFormIndex);
                        var $topicArticleSubmission = $('#TopicSubmissionTable_' + sParentId);

                        //$topicsContainer.insertAfter();
                        $(data.BlockHTMLCode).insertAfter($topicsContainer);
                        var $topicArticle = $('#TopicTable_' + sTopicId);

                        $("#addTopicFormContainer_" + sParentId + '_' + sFormIndex).empty();

                        scrollToElement($topicArticle);
                    }
                    else
                    if (sActionName == "editForumTopic") {
                        var $topicArticleSubmission = $('#TopicSubmissionTable_' + sTopicId);
                        var $topicArticle = $('#TopicBody_' + sTopicId);

                        if ($topicArticle.length)
                            $topicArticle.empty();

                        $topicArticle.append(data.BlockHTMLCode);

                        showArticle(sTopicId,true);

                        scrollToElement($topicArticle);
                    }

                    console.log($topicArticle);
                    console.log('Article Submission'); console.log($topicArticleSubmission);
                }

                showMessageAlert('addForumTopicAlertsContainer_'+sParentId+'_'+sFormIndex, 'success', 'Success', data.message);
            }

        },
        error: function() {

            showMessageAlert('addForumTopicAlertsContainer_'+sParentId+'_'+sFormIndex,'danger','Internal Problem publishing comment','There was an internal problem creating a new topic. <strong>Try again</strong>');
            showAddTopicSubmissionDiv(true, sParentId, sFormIndex);

            return false;
        }
    });

    return true;
}

function titleKeyDown(titleElement, sParentId, sFormIndex)
{
    titleElement = $(titleElement);

    var sText =  $("<div/>").html(titleElement.val()).text();
    var iTitleLength = $.trim(sText).length;
    var $titleLabel = $("#addForumTopicTitleCountLabel_"+sParentId+"_"+sFormIndex);

    $titleLabel.text("Title: "+iTitleLength+" chars");
    if (iTitleLength < 60) $titleLabel.css("color","black");
    else $titleLabel.css("color","red");

}

function showAddTopicSubmissionDiv(bShow, sParentId, sFormIndex)
{
    if (typeof bShow === 'undefined') bShow = true;
    if (typeof sParentId === 'undefined') sParentId ='';

    var p = "#addForumTopicLoadingDiv_"+sParentId+'_'+sFormIndex;
    if (bShow) $(p).hide();
    else $(p).show();

    p = "#addForumTopicSubmissionDiv_"+sParentId+'_'+sFormIndex;
    if (bShow) $(p).show();
    else $(p).hide();
}

function showHideAddTopicForm(bShow, sParentId, sFormIndex)
{
    $addTopicChevron = $("#addTopicFormChevron_"+sParentId+"_"+sFormIndex);

    if (bShow)  slideDownPanel($addTopicChevron);
    else slideUpPanel($addTopicChevron);
}

function addForumTopicReset(sParentId, sFormIndex)
{
    console.log($("#addTopicForm_"+sParentId+"_"+sFormIndex));
    console.log('#addTopicForm_'+sParentId+"_"+sFormIndex+'"');
    $("#addTopicForm_"+sParentId+"_"+sFormIndex)[0].reset();
    document.getElementById("addTopicForm_"+sParentId+"_"+sFormIndex).reset();
}

function addForumTopicCancel (sTopicId, sFormIndex)
{
    showHideAddTopicForm(false, sTopicId, sFormIndex);
}

function showArticle(sTopicId, bVisible)
{
    var $topicArticle = $('#TopicBody_'+sTopicId);
    var $topicSubmission = $('#TopicSubmissionForm_'+sTopicId);

    if (bVisible) {
        $topicArticle.show();
        $topicSubmission.hide();
    } else
    {
        $topicArticle.hide();
        $topicSubmission.show();
    }
}