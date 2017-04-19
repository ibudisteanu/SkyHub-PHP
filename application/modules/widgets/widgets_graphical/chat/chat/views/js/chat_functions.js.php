window['ChatOpenedConversations'] = [];
window['ChatOpenedConversationsIds'] = [];
window['ChatOpenedConversationsClosing'] = [];

//creates a Chat Dialog using a ConversationId
function createShowChatDialog(sConversationId, iMaximizationStatus)
{
    if (checkOpenedChat(sConversationId))
        return false;

    if (typeof iMaximizationStatus==='undefined') iMaximizationStatus = 1;

    $.ajax(
        {
            url: document.location.origin+"/api/chat/get/get-chat-embedded-code/"+sConversationId,
            type: 'post',
            data: {},
            success: function( outputJSONData )
            {
                var outputData = $.parseJSON(outputJSONData);

                if ((outputData.result == true)&&(!checkOpenedChat(sConversationId))){

                    var $chatLoaderContainer = $("#chatLoaderContainer");

                    $chatLoaderContainer.append(outputData.sEmbeddedCode);

                    window['ChatOpenedConversationsIds'].push(sConversationId);

                    window['ChatOpenedConversations'][sConversationId]=[];
                    window['ChatOpenedConversations'][sConversationId]['ConversationId'] = sConversationId;
                    window['ChatOpenedConversations'][sConversationId]['MyUserAvatar']=outputData.sMyUserAvatar;
                    window['ChatOpenedConversations'][sConversationId]['MyUserFullName']=outputData.sMyUserFullName;
                    window['ChatOpenedConversations'][sConversationId]['LastMessageDate']=outputData.dtLastMessageDate;
                    window['ChatOpenedConversations'][sConversationId]['FirstMessageDate']=outputData.dtFirstMessageDate;
                    window['ChatOpenedConversations'][sConversationId]['chatDialogInformation']=outputData.chatDialogInformation;
                    window['ChatOpenedConversations'][sConversationId]['NewTemporaryMessages']=[];

                    scrollConversation(sConversationId);

                    maximizationChatDialog(sConversationId,iMaximizationStatus);

                    console.log('Creating and show a Chat DIALOG '+sConversationId);// successfully
                }
                else {//Error
                    if ((!checkOpenedChat(sConversationId)))
                        console.log('CHAT DIALOG already opened: '+sConversationId);
                    else
                        console.log('Error creating and showing a CHAT DIALOG: '+sConversationId);
                }
            },
            error: function() {
                console.log('Internal Error sending the request to get the embedded code for the Chat Dialog'+sConversationId);
            }
        });
}

//load all opening chats
function createNewChatWithAuthor (Authors, iMaximizationStatus)
{
    if (typeof iMaximizationStatus === 'undefined') iMaximizationStatus = 1;
    if (typeof Authors === 'string') var myData = Authors;
    else
        if (Authors.isArray)
            myData = JSON.stringify(Authors);
        else
            return false;

    $.ajax(
        {
            url: document.location.origin+"/api/chat/get/get-create-conversation-id-with-authors",
            type: 'post',
            data: {
                Authors : myData
            },
            success: function( outputJSONData )
            {
                var outputData = $.parseJSON(outputJSONData);

                if (outputData.result == true){ // successfully

                    createShowChatDialog(outputData.sConversationId,iMaximizationStatus);

                    console.log('Creating New Conversation Id: ' + outputData.sConversationId);
                }
                else {//Error
                    console.log("Conversation Id couldn't be returned");
                }
            },
            error: function() {
                console.log("Internal Error getting the Conversation Id");
            }
        });
    return true;
}

//load all opening chats
function getRefreshChats(sConversationId, bGetLastMessages )
{
    var ChatConversationData = [];

    if (sConversationId != '')//Refreshing only a specific chat!
    {
        var obj = {};
        obj['ConversationId']= sConversationId;
        if (bGetLastMessages) obj['LastMessageDate'] = window['ChatOpenedConversations'][sConversationId]['LastMessageDate']; else
        if (!bGetLastMessages) obj['FirstMessageDate'] = window['ChatOpenedConversations'][sConversationId]['FirstMessageDate'];

        ChatConversationData.push(obj);
    } else //Refreshing all Opened Chats
        window['ChatOpenedConversationsIds'].forEach(function (sConversationId)
        {
            var obj = {};
            obj['ConversationId']= window['ChatOpenedConversations'][sConversationId]['ConversationId'];
            if (bGetLastMessages) obj['LastMessageDate'] = window['ChatOpenedConversations'][sConversationId]['LastMessageDate']; else
            if (!bGetLastMessages) obj['FirstMessageDate'] = window['ChatOpenedConversations'][sConversationId]['FirstMessageDate'];

            ChatConversationData.push(obj);
        });

    console.log(ChatConversationData);

    $.ajax(
    {
        url: document.location.origin+"/api/chat/get/refresh-chats",
        type: 'post',
        data: { ChatConversations: ChatConversationData },
        success: function( outputJSONData )
        {
            var outputData = $.parseJSON(outputJSONData);

            if (outputData.result == true){

                var openedChats = outputData.arrOpenedChats;
                openedChats.forEach(function (chat)
                {
                    var sConversationId = chat.ConversationId;
                    if (typeof window['ChatOpenedConversationsClosing'][sConversationId] !== 'undefined')
                    {
                        delete window['ChatOpenedConversationsClosing'][sConversationId];
                    } else
                    {
                        if (!checkOpenedChat(sConversationId))
                            createShowChatDialog(sConversationId,chat.MaximizationStatus);

                        console.log('LENGTH:'+chat.ReturnMessages.length);
                        if (chat.ReturnMessages.length) //rendering received messages
                        {
                            var iReturnMessageIndex=-1;
                            chat.ReturnMessages.forEach(function (recentMessage)
                            {
                                iReturnMessageIndex++;

                                var $messageAlreadyRenderedBefore = $("#ChatMessage_"+recentMessage.sMessageId);
                                if ($messageAlreadyRenderedBefore.length)
                                    $messageAlreadyRenderedBefore.remove();

                                if (recentMessage.enTypeRender == 1) {
                                    renderReplyUserMessage(sConversationId, recentMessage.sMessageBody, recentMessage.sMessageId, recentMessage.sFullName, recentMessage.sUserAvatarLink, recentMessage.dtDateTime, recentMessage.dtFullDateTime, bGetLastMessages);

                                    if ((bGetLastMessages)&&(typeof spawnNotification !== 'undefined' && $.isFunction(spawnNotification)))
                                        if (iReturnMessageIndex >= chat.ReturnMessages.length - chat.chatDialogInformation.iNewMessages ) //showing notifications only for the new Messages
                                        {
                                            spawnNotification('New Message from ' + recentMessage.sFullName, recentMessage.sMessageBody, recentMessage.sUserAvatarLink);

                                            if (typeof ion !== 'undefined') ion.sound.play("bell_ring");
                                        }
                                }
                                else
                                if (recentMessage.enTypeRender == 2)
                                    renderMyMessage(sConversationId,recentMessage.sMessageBody, recentMessage.sMessageId,recentMessage.sFullName,recentMessage.sUserAvatarLink,recentMessage.dtDateTime, recentMessage.dtFullDateTime, bGetLastMessages);
                            });
                        }

                        if (typeof window['ChatOpenedConversations'][sConversationId] !== 'undefined') {

                            var arrData = window['ChatOpenedConversations'][sConversationId];

                            delete(arrData['bLoadingOlderMessagesScroll']);

                            if ((chat.hasOwnProperty('dtLastMessageDate'))&&(arrData['LastMessageDate'].sec < chat.dtLastMessageDate.sec)) arrData['LastMessageDate'] = chat.dtLastMessageDate;
                            if ((chat.hasOwnProperty('dtFirstMessageDate'))&&(arrData['FirstMessageDate'].sec > chat.dtFirstMessageDate.sec )) arrData['FirstMessageDate'] = chat.dtFirstMessageDate;

                            if (typeof (arrData['chatDialogInformationSkipFirstRefresh']) !== 'undefined' )
                                delete arrData['chatDialogInformationSkipFirstRefresh'];
                            else {
                                //reading maximization status
                                if (chat.chatDialogInformation.iConversationMaximizationStatus != arrData['chatDialogInformation'].iConversationMaximizationStatus) {
                                    maximizationChatDialog(sConversationId, chat.chatDialogInformation.iConversationMaximizationStatus);
                                }

                                //reading new messages notification
                                if (chat.chatDialogInformation.iNewMessages != arrData['chatDialogInformation'].iNewMessages) {
                                    refreshNewMessagesNotification(sConversationId, chat.chatDialogInformation.iNewMessages);
                                }
                            }

                            window['ChatOpenedConversations'][sConversationId] = arrData;
                        }
                    }

                    $("#chatDialogLoadingIcon_"+sConversationId).hide();//removing refresh loading
                    $("#chatDialogLoading_"+sConversationId).children('.direct-chat-status').empty(); //removing Error

                });

                console.log('Chats Opened List Received');// successfully
            }
            else {//Error
                console.log('Chat List returned error');

                ChatConversationData.forEach(function (conversation) {
                    var sConversationId = conversation.ConversationId;
                    $("#chatDialogLoadingIcon_"+sConversationId).hide();  //removing refresh loading
                    showChatMessageStatusError($("#chatDialogLoading_"+sConversationId),'<strong> Server returned an error </strong>','',false); //show error
                });
            }

            setTimeout(function(){getRefreshChats('',true)},3000);

        },
        error: function() {
            console.log('Chat List returned internal error');

            ChatConversationData.forEach(function (conversation) {
                var sConversationId = conversation.ConversationId;
                $("#chatDialogLoadingIcon_"+sConversationId).hide(); //removing refresh loading
                showChatMessageStatusError($("#chatDialogLoading_"+sConversationId),'<strong> Internet problems </strong>','',false); //show error
            });
            setTimeout(function(){getRefreshChats('',true)},3000);
        }
    });
}

setTimeout(function(){getRefreshChats('',true)},3000);

//check if chat was already opened
function checkOpenedChat(sConversationId)
{
    var $chatDialog = $("#chatDialog_"+sConversationId);

    if ($chatDialog.length > 0) {
        $chatDialog.css("display","inline-block");
        return $chatDialog;
    }
    else return null;
}

function scrollChatDialog($element, sConversationId)
{
    $element = $($element);
    var pos = $element.scrollTop();

    if (pos < 10) {

        $("#chatDialogLoadingIcon_"+sConversationId).css("display","block");

        if (typeof window['ChatOpenedConversations'][sConversationId]['bLoadingOlderMessagesScroll'] === 'undefined') {
            window['ChatOpenedConversations'][sConversationId]['bLoadingOlderMessagesScroll']=true;
            getRefreshChats(sConversationId, false);

            console.log('getting new SCROLL UP');
        }
    }
}

function sendMessageChat(sConversationId)
{
    var sMessageBody = $("#chatDialogSendText_"+sConversationId).val();
    var sNewChatTemporaryMessageId = generateTemporaryId(16);

    renderMyMessage(sConversationId, sMessageBody, sNewChatTemporaryMessageId );

    window['ChatOpenedConversations'][sConversationId]['NewTemporaryMessages'][sNewChatTemporaryMessageId]=[];
    window['ChatOpenedConversations'][sConversationId]['NewTemporaryMessages'][sNewChatTemporaryMessageId]['sMessageBody']=sMessageBody;

    sendMessageChatAJAX(sConversationId, sNewChatTemporaryMessageId, sMessageBody );

    $("#chatDialogSendText_"+sConversationId).val('');
}

function sendMessageChatAJAX(sConversationId, sNewChatTemporaryMessageId, sMessageBody)
{
    $newMessageRenderedElement = $("#ChatMessage_"+sNewChatTemporaryMessageId);

    $.ajax(
        {
            url: document.location.origin+"/api/chat/post/post-message-chat",
            type: 'post',
            data: {
                ConversationId : sConversationId,
                MessageBody : sMessageBody
            },
            success: function( outputJSONData )
            {
                var outputData = $.parseJSON(outputJSONData);

                if (outputData.result == true){

                    $newMessageRenderedElement.attr('id','ChatMessage_'+outputData.sNewMessageChatId);
                    closeChatMessageStatus($newMessageRenderedElement);
                }
                else { //Error
                    showChatMessageStatusError($newMessageRenderedElement,'<div style="cursor: pointer;" onClick="sendMessageChatAgain(\''+sConversationId+'\',\''+sNewChatTemporaryMessageId+'\')"><strong>Message</strong> not received correctly. Try again. <i class="fa fa-refresh"> </i></div>',sConversationId);
                }
            },
            error: function() {
                showChatMessageStatusError($newMessageRenderedElement,'<div style="cursor: pointer;" onClick="sendMessageChatAgain(\''+sConversationId+'\',\''+sNewChatTemporaryMessageId+'\')"><strong>Internet problem.</strong> Try again. <i class="fa fa-refresh"> </i></div>',sConversationId);
            }
        });
}

function sendMessageChatAgain(sConversationId, sNewChatTemporaryMessageId)
{
    sendMessageChatAJAX(sConversationId, sNewChatTemporaryMessageId, window['ChatOpenedConversations'][sConversationId]['NewTemporaryMessages'][sNewChatTemporaryMessageId]['sMessageBody']);
}

function generateTemporaryId($iCount)
{
    var text = ""; var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < $iCount; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function messageChatOnKeyPress(sConversationId)
{
    console.log(event.keyCode != 13);
    if (event.keyCode == 13)
    {
        event.preventDefault();
        sendMessageChat(sConversationId);

        return false;
    } else
        return true;

}

function renderMyMessage(sConversationId, sMessageBody, sMessageId, sFullName, sAvatarImageLink, sDateTime, sFullDateTime, bShowAsLastMessages)
{
    if (typeof bShowAsLastMessages == 'undefined') bShowAsLastMessages = true;
    if (typeof sDateTime === 'undefined')
    {
        var dt = new Date($.now());
        sDateTime = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
        sFullDateTime = dt.toString();
    }
    if (typeof sMessageId === 'undefined') sMessageId='';

    if (typeof sAvatarImageLink === 'undefined') sAvatarImageLink = window['ChatOpenedConversations'][sConversationId]['MyUserAvatar'];
    if (typeof sFullName === 'undefined') sFullName = window['ChatOpenedConversations'][sConversationId]['MyUserFullName'];

    var sHTMLCode = <?php echo $this->StringsAdvanced->convertMultiLineStringToJavaScript($this->load->view('message/html-js-templates/message_template_js_right_view.php', null, TRUE)) ?>;
    var $newMessageRenderedElement = $(sHTMLCode);

    if (bShowAsLastMessages){
        $("#chatConversation_"+sConversationId).append($newMessageRenderedElement);
        scrollConversation(sConversationId, true);
    }
    else $("#chatConversation_"+sConversationId).prepend($newMessageRenderedElement);

    return $newMessageRenderedElement;

}

function renderReplyUserMessage(sConversationId, sMessageBody, sMessageId, sFullName, sAvatarImageLink, sDateTime, sFullDateTime, bShowAsLastMessages) {
    if (typeof bShowAsLastMessages == 'undefined') bShowAsLastMessages = true;

    var sHTMLCode = <?php echo $this->StringsAdvanced->convertMultiLineStringToJavaScript($this->load->view('message/html-js-templates/message_template_js_left_view.php', null, TRUE)) ?>;
    var $newMessageRenderedElement = $(sHTMLCode);

    if (bShowAsLastMessages) {
        $("#chatConversation_"+sConversationId).append($newMessageRenderedElement);
        scrollConversation(sConversationId, true);
    }
    else $("#chatConversation_"+sConversationId).prepend($newMessageRenderedElement);

    return $newMessageRenderedElement;
}

function scrollConversation(sConversationId, bDirectionDown) {
    if (typeof bDirectionDown === 'undefined') bDirectionDown = true; //Down;

    var $chatConversation = $("#chatConversation_" + sConversationId);
    if ($chatConversation.length) {

        if (bDirectionDown) $chatConversation.scrollTop($($chatConversation)[0].scrollHeight);
        else $chatConversation.scrollTop(0);
    }
    console.log('scroll down...');
}

function showChatMessageStatusError($newMessageRenderedElement, sMessage, sConversationId, bScrollDown)
{
    if (typeof bScrollDown == 'undefined') bScrollDown = true;

    $chatStatus = $newMessageRenderedElement.children('.direct-chat-status');

    $chatStatus.show();
    $chatStatus.empty();
    $chatStatus.append( '<div class="alert alert-danger" style="padding:5px">'+sMessage+ '</div>');

    if (bScrollDown)
        scrollConversation(sConversationId);
}

function closeChatMessageStatus($newMessageRenderedElement)
{
    $newMessageRenderedElement.children('.direct-chat-status').hide();
}

function resetNewMessagesNotification(sConversationId)
{
    if (typeof (window['ChatOpenedConversations'][sConversationId]) !== 'undefined')
    $.ajax(
        {
            url: document.location.origin+"/api/chat/post/reset-new-messages-notification",
            type: 'post',
            data: { ConversationId : sConversationId },
            success: function( outputJSONData )
            {
                var outputData = $.parseJSON(outputJSONData);

                if (outputData.result == true){

                    refreshNewMessagesNotification(sConversationId,0);
                    window['ChatOpenedConversations'][sConversationId]['chatDialogInformation'].iNewMessages = 0;

                    console.log('Chats Reset New Messages Notification');// successfully
                }
                else {//Error
                    console.log('Error resetting New Messages Notification');
                }
            },
            error: function() {
                console.log('Chat Error resetting New Messages Notification');
            }
        });
}

function refreshNewMessagesNotification(sConversationId, iNewMessages )
{
    var $chatNewMessages = $("#chatNewMessages_"+sConversationId);
    if ($chatNewMessages.length)
    {
        if (iNewMessages > 0)
        {
            $chatNewMessages.show();
            $chatNewMessages.attr("title",iNewMessages+" New Messages");
            $chatNewMessages.html(iNewMessages);
        } else
            $chatNewMessages.hide();
    }

    if (typeof (window['ChatOpenedConversations'][sConversationId]) !== 'undefined')
        window['ChatOpenedConversations'][sConversationId]['chatDialogInformation'].iNewMessages = iNewMessages;
}

function maximizationChatDialogButtonClick(sConversationId)
{
    if (window['ChatOpenedConversations'][sConversationId]['chatDialogInformation'].iConversationMaximizationStatus == 0) var iNewMaximizationValue=1;
    else var iNewMaximizationValue=0;

    window['ChatOpenedConversations'][sConversationId]['chatDialogInformationSkipFirstRefresh']=true;
    maximizationChatDialog(sConversationId,iNewMaximizationValue);

    $.ajax(
        {
            url: document.location.origin+"/api/chat/post/change-maximization-chat-dialog-status",
            type: 'post',
            data: {
                ConversationId : sConversationId,
                NewMaximizationValue : iNewMaximizationValue
            },
            success: function( outputJSONData )
            {
                var outputData = $.parseJSON(outputJSONData);

                if (outputData.result == true){

                    window['ChatOpenedConversations'][sConversationId]['chatDialogInformationSkipFirstRefresh']=true;
                    maximizationChatDialog(sConversationId,iNewMaximizationValue);

                    console.log('Maximization Status Changed successfully');// successfully
                }
                else {//Error
                    console.log('Maximization Status Changed error');
                }
            },
            error: function() {
                console.log('Maximization Status Changed Internal error');
            }
        });
}

function closeChatDialogButtonClick(sConversationId)
{
    if (typeof window['ChatOpenedConversations'][sConversationId] !== 'undefined')
    {
        removeChatDialog(sConversationId);
        $.ajax(
            {
                url: document.location.origin+"/api/chat/post/close-chat-conversation",
                type: 'post',
                data: {
                    ConversationId : sConversationId,
                },
                success: function( outputJSONData )
                {
                    var outputData = $.parseJSON(outputJSONData);

                    if (outputData.result == true){

                        removeChatDialog(sConversationId);

                        console.log('Close Chat Conversation '+sConversationId);// successfully
                    }
                    else {//Error
                        console.log('Error closing Chat Conversation '+sConversationId);
                    }
                },
                error: function() {
                    console.log('Error closing Chat Conversation '+sConversationId);
                }
            });
    }
}

function removeChatDialog(sConversationId)
{
    window['ChatOpenedConversationsClosing'][sConversationId]=true;

    var $chatDialogDiv = $("#chatDialogDiv_"+sConversationId);
    if ($chatDialogDiv.length)
        $chatDialogDiv.remove();


    if (typeof window['ChatOpenedConversations'][sConversationId] !== 'undefined')
    {
        delete window['ChatOpenedConversations'][sConversationId];
        for (var i=0; i < window['ChatOpenedConversationsIds'].length; i++)
            if (window['ChatOpenedConversationsIds'][i] == sConversationId) {
                delete window['ChatOpenedConversationsIds'][i];
                break;
            }
    }
}

function maximizationChatDialog(sConversationId, iMaximizationStatus )
{
    if (iMaximizationStatus == 0) //minimization
    {
        $("#chatDialogBody_"+sConversationId).hide();//.css("display", "none");
        $("#chatDialogFooter_"+sConversationId).hide();//.css("display", "none");
        $("#chatDialogChangeMaximizationButton_"+sConversationId).html('<i class="fa fa-plus"></i>');

        $("#chatDialog_"+sConversationId).css("margin-top", "310px");
    } else
    if (iMaximizationStatus == 1)
    {
        $("#chatDialogBody_"+sConversationId).css("display", "inline-block");
        $("#chatDialogFooter_"+sConversationId).css("display", "inline-block");
        $("#chatDialogChangeMaximizationButton_"+sConversationId).html('<i class="fa fa-minus"></i>');

        $("#chatDialog_"+sConversationId).css("margin-top", "0");
    }

    window['ChatOpenedConversations'][sConversationId]['chatDialogInformation'].iConversationMaximizationStatus = iMaximizationStatus;
}