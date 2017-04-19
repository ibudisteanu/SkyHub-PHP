
function getCaretPosition(ctrl) {
    var start, end;
    if (ctrl.setSelectionRange) {
        start = ctrl.selectionStart;
        end = ctrl.selectionEnd;
    } else if (ctrl && ctrl.createRange) {
        var range = ctrl.createRange();
        start = 0 - range.duplicate().moveStart('character', -100000);
        end = start + range.text.length;
    }
    return {
        start: start,
        end: end
    }
}

function insertTag(Strings, tag) {
    code = Strings[0] + '<span contentEditable = "false" class = "highlight">' + tag + '</ span> & nbsp;' + Strings[1];
    return code;
}

function addReplyButtonClick(objectButton, sTitle, sParentId, sGrandParentId, sActionName)
{
    var $commentStatus = $('#commentStatus'+sParentId); $commentStatus.empty();

    var sFormAction = document.location.origin+'/api/reply/post/processReplySubmit';
    var sHTMLBody = <?= $this->StringsAdvanced->convertMultiLineStringToJavaScript($this->load->view('add_reply_inline/js/add_reply_text_editor_view.php', null, TRUE)) ?>;

    var $inputReplyBox = $('#addReplyBox' + sParentId);

    if ($inputReplyBox.html().length == 0)  //For the first time
        $inputReplyBox.append(sHTMLBody);
    else
    if ($("#addReplyAction_"+sParentId+"_"+sGrandParentId).value != sActionName) {
        /*//TO CLEAR THE PREVIOUS CONTENT
         $inputReplyBox.empty();
         $inputReplyBox.append(sHTMLBody);*/
    }

    $inputReplyBox.show();
    scrollToElement($(("#AddReply"+sParentId+"_"+sGrandParentId)));

    showHideAddReplyForm(true, sParentId,sGrandParentId);

    $('#addForumReplyMessageCode' + sParentId).summernote({
        height: 150,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: true,                  // set focus to editable area after initializing summernote

        hint:
            [
                {
                    match: /:([\-+\w]+)$/,
                    search: function (keyword, callback) {
                        callback($.grep(emojis, function (item) {
                            return item.indexOf(keyword) === 0;
                        }));
                    },
                    template: function (item) {
                        var content = emojiUrls[item];
                        return '<img src="' + content + '" style="max-height:16px" /> :' + item + ':';
                    },
                    content: function (item) {
                        console.log(item);
                        var url = emojiUrls[item];
                        if (url) {
                            return $('<img />').attr({'src': url,'emoji':'enabled'}).css('max-height', 16)[0];
                        }
                        return '';
                    }
                },
                //http://stackoverflow.com/questions/34883780/jquery-promises-with-summernote-hints
                {
                    mentions: ['jayden', 'sam', 'alvin', 'david'],
                    match: /\B@(\w*)$/,
                    search: function (keyword, callback) {
                        callback($.grep(this.mentions, function (item) {
                            return item.indexOf(keyword) == 0;
                        }));
                    },
                    content: function (item) {
                        return '@@@@' + item;
                    }
                }
            ],
        callbacks: {

            onpaste: function(content) {
                setTimeout(function () {
                    editor.code(content.target.textContent);
                }, 10);
            }
        }
    });

    return false;
}

function editReplyClick(objectButton, sTitle, sTopicId, sGrandParentId)
{
    addReplyButtonClick(null, sTitle, sTopicId, sGrandParentId,'edit-reply');

    var $inputReplyBox = $('#addReplyBox'+sTopicId);

    $('#addForumReplyTitle'+sTopicId).val($.trim($("#replyTitle"+sTopicId).html()));
    $('#addForumReplyMessageCode' + sTopicId).summernote('code',$('#replyBody' +sTopicId).html());

}

function submitReplyClick(objectButton, sTitle, sParentId, sGrandParentId, sActionName)
{
    //for sActionName ==  'add-reply'  sParentId is actually the Id of the Parent (it could be a topic, a reply, a different object)
    //for sActionName ==  'edit-reply' sParentId is actually the Id of the Topic (sTopicId);
    if (sActionName == 'edit-reply') var sTopicId = sParentId;

    var $inputReplyBox = $('#addReplyBox'+sParentId);

    var $commentStatus = $('#commentStatus'+sParentId); $commentStatus.empty();

    var sFormTitle =  $('#addForumReplyTitle'+sParentId).val();
    var sFormMessageBody = $('#addForumReplyMessageCode'+sParentId).summernote('code');
    //console.log("The message is", sFormMessageBody);

    var sError = '';var sSuccess = '';

    //console.log(sFormTitle);

    //if ($('<b>'+sFormTitle+'</b>').text().length == 0) sError +=  '<strong>Title</strong> is empty<br/>';

    if ($('<b>'+sFormMessageBody+'</b>').text().length == 0) sError += '<strong>Message</strong> is empty<br/>';

    var $replyStatus = $('#replyStatus'+sParentId); $replyStatus.html('');
    if (sError != '')
    {
        showMessageAlert('replyStatus'+sParentId,'danger','Comment problem',sError,"margin-bottom:10px");
        return false;
    }

    showReplySubmissionDiv(false, sParentId, sGrandParentId);

    //console.log('ParentId '+iParentId);
    //console.log('GrandParentId '+iGrandParentId);

    $.ajax({
        type: 'POST',
        url: document.location.origin+'/api/reply/post/processReplySubmit',
        data: {
            'Action': sActionName,
            'ParentId': sParentId,
            'GrandParentId': sGrandParentId,
            'Title': sFormTitle,
            'MessageCode': sFormMessageBody
        },
        success: function( data ) {

            var data = $.parseJSON(data);
            //console.log(data);

            if (data.status == 'error') {
                showMessageAlert("replyStatus"+sParentId, 'danger', 'Problems publishing comment', data.message,"margin-bottom:10px");

                if (!data.logged)
                    openLoginPopupAuthentication();
            }
            else
            if (data.status == 'success')
            {
                var $repliesNewContainer = $('#repliesNewContainer'+sParentId);
                //console.log(data.BlockHTMLCode);

                $inputReplyBox.hide(); $inputReplyBox.empty();

                //console.log(data.BlockHTMLCode);
                if (sActionName == 'add-reply') //The user just added a new Reply, so the browser must display the new reply
                {
                    if (data.BlockHTMLCode.length > 0)
                    {
                        //console.log(repliesNewContainer);
                        $(data.BlockHTMLCode).insertBefore($repliesNewContainer);

                        $('#timelineSubReplies'+sParentId).show();

                        scrollToElement("#"+data.BlockHTMLTagId);

                        console.log('#Vote'+data.sVotingId);
                        console.log(data.sVotingId);
                        console.log(data.BlockHTMLTagId.toString());
                        console.log(("processingVoteCallback"+sGrandParentId.toString()).toString());

                        $('#Vote'+data.sVotingId).upvote ( {id : data.sVotingId, parentObjectId : "replyId"+data.BlockHTMLTagId.toString(), grandParentObjectId : sGrandParentId.toString(), callback: "processingVoteCallback" });

                    }
                } else
                if (sActionName == 'edit-reply') //The user just edited the current Reply, so the browser must display the edited reply
                {
                    $("#replyId"+sTopicId).replaceWith(data.BlockHTMLCode);

                    /*$("#replyTitle"+sTopicId).html($('#addForumReplyTitle'+sTopicId));
                     $('#replyBody' +sTopicId).html($('#addForumReplyMessageCode' + sTopicId).summernote('code'));*/
                }

                showMessageAlert("replyStatus"+sParentId, 'success', 'Success', data.message,"margin-bottom:10px");
            }
            showReplySubmissionDiv(true, sParentId, sGrandParentId);
        },
        error: function() {
            console.log('errror reply');

            showMessageAlert("replyStatus"+sParentId,'danger','Internal Problem publishing comment','There was an internal problem publishing your comment. <strong>Try again</strong>',"margin-bottom:10px");
            showReplySubmissionDiv(true, sParentId, sGrandParentId);

            return false;
        }
    });
}

function showReplySubmissionDiv(bShow, sParentId, sGrandParentId)
{
    if (typeof bShow === 'undefined') bShow = true;

    var $p = $("#replyLoadingDiv_"+sParentId+"_"+sGrandParentId);
    if (bShow) $p.hide();
    else $p.show();

    $p = $("#replySubmissionDiv_"+sParentId+"_"+sGrandParentId);
    if (bShow) $p.show();
    else $p.hide();
}

function deleteReplyClick (object, sTitle, sId, sGrandParentId)
{

    var $input = $('#addReplyBox'+sId); $input.hide();
    var $input = $('#addReplyBox'+sGrandParentId); $input.hide();

    showReplySubmissionDiv(false, sId, sGrandParentId);

    $.ajax({
        type: 'POST',
        url: document.location.origin+'/api/reply/post/processReplySubmit',
        data: {
            'Action': 'delete-reply',
            'ParentId': sId,
            'GrandParentId': sGrandParentId,
            'Title': "JUST DELETE",
            'MessageCode': "JUST DELETE"
        },
        success: function( data ) {
            var data = $.parseJSON(data);
            if (data.status == 'error')
                showMessageAlert("commentStatus"+sId,'danger','Problems deleting your comment',data.message,"margin-top:15px; margin-bottom:15px");
            else
            if (data.status == 'success')
            {
                var $replyBox = $('#replyId'+sId);
                $replyBox.hide(); $replyBox.empty();

                showMessageAlert("commentStatus"+data.ParentId, 'success', 'Success', data.message,"margin-top:15px; margin-bottom:15px");

                if (data.NoSubReplies==0)
                    $('#timelineSubReplies'+data.ParentId).hide();

                scrollToElement("#replyId"+data.ParentId);

            }
            showReplySubmissionDiv(true, sId, sGrandParentId);
        },
        error: function() {
            showMessageAlert("commentStatus"+sId,'danger','Internal Problem deleting comment','There was an internal problem deleting your comment. <strong>Try again</strong>',"margin-top:15px; margin-bottom:15px");
            showReplySubmissionDiv(true, sId, sGrandParentId);
            return false;
        }
    });

}

function showHideAddReplyForm(bShow, sParentId, sGrandParentId)
{
    $addReplyChevron = $("#addReplyFormChevron_"+sParentId+"_"+sGrandParentId);

    if ($addReplyChevron.length)
    {
        if (bShow)  slideDownPanel($addReplyChevron);
        else slideUpPanel($addReplyChevron);
    }
}

function cancelReplyClick(objectButton, sTitle, sParentId, sGrandParentId)
{
    showHideAddReplyForm(false, sParentId, sGrandParentId);
}