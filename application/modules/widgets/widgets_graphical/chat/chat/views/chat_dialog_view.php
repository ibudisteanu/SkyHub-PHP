<!-- DIRECT CHAT PRIMARY -->
<div id="chatDialogDiv_<?=$sConversationId?>" class="col-xs-6 col-sm-4 col-md-3 col-lg-2" style="padding-right:0; min-width:210px; display:inline-block; float: right !important;">

    <div id="chatDialog_<?=$sConversationId?>"class="box box-primary direct-chat direct-chat-primary <?=$chatDialogInformation['iConversationMaximizationStatus'] == TOpenedConversationMaximizationStatus::conversationMinimized ? ' collapsed-box' :''?> " onClick="resetNewMessagesNotification('<?=$sConversationId?>')" style="">
        <div id="chatDialogHeader_<?=$sConversationId?>" class="box-header with-border">
            <h3 class="box-title"><?=$this->ViewConversationController->renderConversationTitle($Conversation)?></h3>

            <div class="box-tools pull-right">
                <span id="chatNewMessages_<?=$sConversationId?>" data-toggle="tooltip" title="<?=$chatDialogInformation['iNewMessages']?> New Messages" class="badge bg-light-blue" <?=$chatDialogInformation['iNewMessages']== 0 ? 'style="display:none"' : '' ?>><?=$chatDialogInformation['iNewMessages']?></span>
                <button id="chatDialogChangeMaximizationButton_<?=$sConversationId?>" type="button" onClick="maximizationChatDialogButtonClick('<?=$sConversationId?>')" class="btn btn-box-tool"><i class="<?=$chatDialogInformation['iConversationMaximizationStatus'] == TOpenedConversationMaximizationStatus::conversationMaximized ? 'fa fa-minus' : 'fa fa-plus'?>"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                    <i class="fa fa-comments"></i></button>
                <button type="button" class="btn btn-box-tool" onClick="closeChatDialogButtonClick('<?=$sConversationId?>')" ><i class="fa fa-times"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div id="chatDialogBody_<?=$sConversationId?>" class="box-body"  style=" width:100%; <?=$chatDialogInformation['iConversationMaximizationStatus'] == TOpenedConversationMaximizationStatus::conversationMinimized ? "display:none" :''?>"  >
            <!-- Conversations are loaded here -->

            <div id="chatDialogLoading_<?=$sConversationId?>" style="text-align: center">
                <i id="chatDialogLoadingIcon_<?=$sConversationId?>" class="fa fa-refresh fa-spin">&nbsp;</i>

                <div class="direct-chat-status" style="margin-top: 10px">
                </div>

            </div>

            <div id="chatConversation_<?=$sConversationId?>" onscroll="scrollChatDialog(this,'<?=$sConversationId?>')" class="direct-chat-messages" style="padding: 0 5px 0 5px;">
                <?php //$this->ViewConversationController->renderConversation($Conversation) ?>
            </div>
            <!--/.direct-chat-messages-->

            <!-- Contacts are loaded here -->
            <div class="direct-chat-contacts">
                <ul class="contacts-list">

                    <?php
                        foreach ($Conversation->arrAuthors as $sAuthor)
                        {
                            $authorUser = $this->UsersMinimal->userByMongoId($sAuthor);

                            if ($authorUser != null) : ?>

                                <li>
                                    <a href="<?=base_url('users/'.$authorUser->getUserLink())?>">
                                        <img class="contacts-list-img" src="<?=$authorUser->sAvatarPicture?>" alt="User Image">

                                        <div class="contacts-list-info">
                                        <span class="contacts-list-name">
                                          <?=$authorUser->getFullName()?>
                                            @ <?=$authorUser->sUserName?>
                                          <small class="contacts-list-date pull-right"><?=$authorUser->getCreationDateString()?></small>
                                        </span>
                                        <span class="contacts-list-msg">
                                            <?php
                                                $arrLastMessages = $Conversation->getLastMessagesWrittenByAuthor($sAuthor,3);
                                                foreach ($arrLastMessages as $lastMessage)
                                                    echo $lastMessage->sBody.'<br/>';
                                            ?>
                                        </span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>

                    <?php
                            endif ;
                        }
                    ?>

                    <!-- End Contact Item -->
                </ul>
                <!-- /.contatcts-list -->
            </div>
            <!-- /.direct-chat-pane -->
        </div>
        <!-- /.box-body -->
        <div id="chatDialogFooter_<?=$sConversationId?>" class="box-footer" <?=$chatDialogInformation['iConversationMaximizationStatus'] == TOpenedConversationMaximizationStatus::conversationMinimized ? "style='display:none'" :''?> >
            <form action="#" method="post">
                <div class="input-group">
                    <input id="chatDialogSendText_<?=$sConversationId?>" onKeyPress="messageChatOnKeyPress('<?=$sConversationId?>')" type="text" name="message" placeholder="Type Message ..." class="form-control">
                    <span class="input-group-btn">
                        <button type="button" id="chatDialogSendButton_<?=$sConversationId?>" onClick="sendMessageChat('<?=$sConversationId?>')" class="btn btn-primary btn-flat">Send</button>
                    </span>
                </div>
            </form>
        </div>
        <!-- /.box-footer-->
    </div>
    <!--/.direct-chat -->
</div>