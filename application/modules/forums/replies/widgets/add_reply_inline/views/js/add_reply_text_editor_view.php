<a class="anchor" id="AddReply'+sParentId+'_'+sGrandParentId+'"></a>
<div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center; padding:10px 0px 0px 0px; ">
    <div class="panel panel-primary" style="background: #eeeeee; border: 1px solid #b7b5b5; ">
        <div class="panel-heading">

            <h3 class="panel-title"><i class="ion ion-clipboard"></i> <strong>'+(sActionName == 'add-reply' ? 'Reply' : 'Edit Reply')+'</strong> to <strong>'+sTitle+'</strong></h3>

            <span id="addReplyFormChevron_'+sParentId+'_'+sGrandParentId+'" bodyName="addReplyFormBody_'+sParentId+'_'+sGrandParentId+'" class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>

        </div>
        <!-- /.box-header -->
        <div id="addReplyFormBody_'+sParentId+'_'+sGrandParentId+'" class="box-body">

            <input type="hidden" id="addReplyAction_'+sParentId+'_'+sGrandParentId+'" value="'+sActionName+'">

            <form class="form-horizontal" action="'+sFormAction+'" role="form" method="post" enctype= "multipart/form-data">

                <div id="replyStatus'+sParentId+'" style="margin:0"></div>

                <div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center; padding:0">

                    <div class="form-group">
                        <label class="col-lg-1 control-label">Title:</label>
                        <div class="col-lg-11">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                                <input type="text" class="form-control" id="addForumReplyTitle'+sParentId+'" name="addForumReplyTitle'+sParentId+'" placeholder="Topic Title" required>
                            </div>

                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: -20px;">
                        <label class="col-lg-1 control-label">Message:</label>
                        <div class="col-lg-11">
                            <textarea class="input-block-level" contenteditable="true"  id="addForumReplyMessageCode'+sParentId+'" name="addForumReplyMessageCode'+sParentId+'" rows="18"><p></p></textarea>
                        </div>
                    </div>

                    <div id="replyLoadingDiv_'+sParentId+'_'+sGrandParentId+'"  style="padding-top:10px; text-align: center; font-size: 30px; display: none">
                        <i class="fa fa-refresh fa-spin" style=" padding-bottom:10px; height: 30px;"></i>
                    </div>

                    <div id="replySubmissionDiv_'+sParentId+'_'+sGrandParentId+'" class="form-group" >
                        <div class="col-md-12" style="text-align: center; padding-top: 10px">

                            <a id="replySubmitButton_'+sParentId+'_'+sGrandParentId+'" class="btn btn-primary"  onClick="submitReplyClick(this,&apos;'+sTitle+'&apos;,&apos;'+sParentId+'&apos;,&apos;'+sGrandParentId+'&apos;,&apos;'+sActionName+'&apos;)" ><i class="glyphicon glyphicon-send" aria-hidden="true"></i> '+(sActionName == 'add-reply' ? 'Send ' : 'Save edit ')+'Reply</a>

                            <span></span>
                            <a id="replyCancelButton_'+sParentId+'_'+sGrandParentId+'" class="btn btn-warning cancel"  onClick="cancelReplyClick(this,&apos;'+sTitle+'&apos;,&apos;'+sParentId+'&apos;,&apos;'+sGrandParentId+'&apos;)" ><i class="fa fa-times" aria-hidden="true"></i> Cancel Reply</a>
                        </div>
                    </div>
                </div>


            </form>

        </div>
        <!-- /.box -->

    </div>
</div>

