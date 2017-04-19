function addTopicFormInlineClick(sFormIndex, sAction, sParentId, sTopicId, sFormResponseType)
{
    if (typeof (sParentId) === 'undefined') sParentId = '';
    if (typeof (sTopicId) === 'undefined') sTopicId = '';
    if (typeof (sFormIndex) === 'undefined') sFormIndex = 0;

    console.log('addTopicFormInlineClick: '+sFormIndex+ ' '+sAction + ' '+sParentId + ' '+sTopicId);

    var $addTopicForm = $('#addTopicFormContainer_'+sParentId+sFormIndex);
    if ($addTopicForm.length)
    {
        $addTopicForm.show();

        var $addTopicFormChevron = $('#addTopicFormChevron_'+sParentId+'_'+sFormIndex);
        console.log($addTopicFormChevron );
        slideDownPanel($addTopicFormChevron);

        scrollToElement($addTopicForm);

        return false;
    }

    $.ajax({
        type: 'POST',
        url: document.location.origin+'/api/topic/post/show-form',
        data: {
            'Action': sAction,
            'ParentId': sParentId,
            'TopicId': sTopicId,
            'FormIndex' : sFormIndex,
            'FormResponseType' : sFormResponseType
        },
        success: function( data ) {
            var data = $.parseJSON(data);

            if (data.result == false)
                showMessageAlert("addTopicFormInlineStatus_"+sParentId+'_'+sFormIndex,'danger','Problems creating the dynamic Add Topic Form',data.message,"margin-top:15px; margin-bottom:15px");
            else
            if (data.result == true)
            {
                var $addTopicForm = $('#addTopicFormInline_'+sParentId+'_'+sFormIndex);
                $addTopicForm.show();
                $addTopicForm.empty();

                ($addTopicForm).append($(data.FormHTMLCode));

                //showMessageAlert("addTopicFormInlineStatus_"+sParentId, 'success', 'Success', data.message,"margin-top:15px; margin-bottom:15px");
                initializeAddForumTopicForm(sParentId, sFormIndex, data.language);

                //scroll
                scrollToElement("#AddTopicForm"+sParentId+sFormIndex);
            }
            //showReplySubmissionDiv(true, iId, iGrandParentId);
        },
        error: function() {
            showMessageAlert("addTopicFormInlineStatus_"+sParentId+'_'+sFormIndex,'danger','Internal Problem creating dynamic Add Topic Form','There was an internal problem creating the dynamic Add Topic Form. <strong>Redirecting...</strong>',"margin-top:15px; margin-bottom:15px");
            if (sAction == 'add-topic')
                $(location).attr('href', document.location.origin+'/forum/category/'+sParentId+'/'+sAction+'/#AddTopic');
            else
                $(location).attr('href', document.location.origin+'/topic/'+sTopicId+'/'+sAction+'/#AddTopic');

            //showReplySubmissionDiv(true, iId, iGrandParentId);
            return false;
        }
    });

    return false;
}

function editTopicFormInlineClick(sParentId, sTopicId, sTopicTitle, sFormResponseType)
{
    if (typeof (sParentId) === 'undefined') sParentId = '';
    if (typeof (sTopicId) === 'undefined') sTopicId = '';
    if (typeof (sTopicTitle) === 'undefined') sTopicTitle = '';

    event.preventDefault();

    console.log('editTopicFormInlineClick: '+sTopicId+ ' '+sParentId);

    var $editTopicForm = $('#addTopicFormContainer_'+sParentId+sTopicId);
    if ($editTopicForm.length)
    {
        $editTopicForm.show();

        var $editTopicFormChevron = $('#addTopicFormChevron_'+sParentId+'_'+sTopicId);
        console.log($editTopicFormChevron );
        slideDownPanel($editTopicFormChevron);

        scrollToElement($editTopicForm);
        return false;
    }

    showArticle(sTopicId,false);

    $.ajax({
        type: 'POST',
        url: document.location.origin+'/api/topic/post/show-form',
        data: {
            'Action': 'edit-topic',
            'ParentId': sParentId,
            'TopicId': sTopicId,
            'FormIndex' : sTopicId,
            'FormResponseType' : sFormResponseType,
        },
        success: function( data ) {
            var data = $.parseJSON(data);
            if (data.result == false) {
                showArticle(sTopicId,true);
                showToolTipTimeOut($('#btTopicEdit' + sTopicId), data.message, 'tooltip-error', '\Error Editing', 'up', true, 10000, true);
            }
            else
            if (data.result == true)
            {
                var $editTopicForm = $('#TopicSubmissionForm_'+sTopicId);
                $editTopicForm.show();
                $editTopicForm.empty();

                $editTopicForm.append($(data.FormHTMLCode));

                console.log($editTopicForm);

                initializeAddForumTopicForm(sParentId, sTopicId, data.language);

                //scroll
                scrollToElement("#AddTopicForm"+sParentId+sTopicId);
            }
        },
        error: function() {
            showArticle(sTopicId,true);
            showToolTipTimeOut($('#btTopicEdit' + sTopicId), 'Internal Problem creating dynamic Add Topic Form','There was an internal problem creating the dynamic Add Topic Form. <strong>Redirecting...</strong>', 'tooltip-error', 'Error Editing', 'up', true, 10000, true);

            $(location).attr('href', document.location.origin+'/topic/'+sTopicId+'/editTopic/#AddTopic');

            return false;
        }
    });

    return false;
}


function deleteTopicClick (sTopicId, sTopicTitle)
{
    var $topicTable = $("#TopicTable_"+sTopicId);

    $.ajax({
        type: 'POST',
        url: document.location.origin+'/api/topic/post/delete',
        data: {
            'TopicId': sTopicId
        },
        success: function( data ) {
            var data = $.parseJSON(data);
            if (data.status == 'error')
                showToolTipTimeOut($('#btTopicDelete' + sTopicId),data.message,'tooltip-error','Error Deleting','up',true,10000,true);
            else
            if (data.status == 'success')
            {
                $topicTable.empty();
                showMessageAlert("TopicTable_"+sTopicId,'success','Topic successfully <strong>deleted</strong>','The topic: "<strong>'+sTopicTitle+'</strong>" has been successfully deleted.',"margin-top:15px; margin-bottom:15px");

                scrollToElement($topicTable);
            }
            //showReplySubmissionDiv(true, iId, iGrandParentId);
        },
        error: function() {
            showToolTipTimeOut($('#btTopicDelete' + sTopicId),"Internal Error - it didn't work <br> The delete function couldn't be performed on the Topic "+sTopicTitle+" c <strong> Try again </strong>",'tooltip-error','Internal Error','up',true,10000,true);
            //showReplySubmissionDiv(true, iId, iGrandParentId);
            return false;
        }
    });

}

function enableTopicDelete(sTopicId)
{

}
