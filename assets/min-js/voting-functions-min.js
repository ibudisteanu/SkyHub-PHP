var processingVoteCallback = function(data) {
    console.log(data);
    $.ajax({
        url: document.location.origin+'/api/voting/post/processVoteSubmit',
        type: 'post',
        data: { id: data.id, parentObjectId: data.parentObjectId,  grandParentId : data.grandParentObjectId,  grandParentType:"topic" , up: data.upvoted, down: data.downvoted, star: data.starred },
        success: function( outputJSONData ) {
            var outputData = $.parseJSON(outputJSONData);

            /*console.log('up'); console.log(outputData.upState);
             console.log('down'); console.log(outputData.downState);
             console.log('star'); console.log(outputData.markedStarState);*/
            //$('#Vote'+data.id).upvote('upvote');

            if (outputData.status == "success"){
                if (outputData.moveToPreviousParentId != '') {//changing the order in real time
                    var temp = $('#'+outputData.objectId).detach();
                    if (outputData.moveToPreviousParentAction == -1)
                        temp.insertBefore($('#'+outputData.moveToPreviousParentId));
                    else if (outputData.moveToPreviousParentAction == 1)
                        temp.insertAfter($('#'+outputData.moveToPreviousParentId));

                    scrollToElement("#"+outputData.objectId);
                }

                showToolTipTimeOut($('#Vote' + data.id),outputData.message,'tooltip-success','Voting Successfully','right',true,3000,true);
            }

            if (outputData.status == "error") {
                showToolTipTimeOut($('#Vote' + data.id),outputData.message,'tooltip-error','Problem voting','right',true,5000);

                /*console.log($('#Vote' + data.id));
                 $('#Vote' + data.id).upvote({
                 upvoted: 0,
                 downvoted: 0,
                 starred: 0
                 }); //count: 5, */
            }
        },
        error: function() {
            //$('#Vote'+data.id).upvoteNoCallback({upvoted: 0, downvoted: 0, starred: 0});
            showToolTipTimeOut($('#Vote' + data.id),"There was a problem submitting your vote",'tooltip-error','Internal problem voting','right',false,5000);
        }
    });
};