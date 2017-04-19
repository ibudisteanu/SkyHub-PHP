<?php if ((!isset($dtForumCategory)) || ($dtForumCategory == null)) return ?>

<?=$bMasonryItem ? '<div class="item" style="left: 0px; top: 0px; width: 100%; ">' : '' ?>

    <table class="table table-hover table-forums" style="width:100%; background-color: white">
        <tbody>
            <?php $sFormIndex = rand(0,100000000) ?>
            <tr id="topicsTable_<?=$dtForumCategory->sID?>_<?=$sFormIndex?>" >
                <th style="text-align: left; padding-left: 5px">

                    <a href="<?=$dtForumCategory->getFullURL()?>">
                        <h3 style="margin:0">
                        <?php
                            if ($dtForumCategory->isIcon($dtForumCategory->sImage))
                            echo '<i class="'.$dtForumCategory->sImage.' table-forums-icon" style="padding-right:5px"></i>';
                            else
                            if ($dtForumCategory->sImage != '') echo '<img src="'.$dtForumCategory->sImage.'" alt="'.$dtForumCategory->sName.'" style="max-width: 42px; max-height: 42px;">';

                            echo $dtForumCategory->sName;
                        ?>

                        <?=$this->AddTopicInlineController->renderTopicAddButton($dtForumCategory->sID, $sFormIndex, "topic-preview-table")?>

                        <?php
                            if ($dtForumCategory->checkOwnership()) : ?>

                            <a href="<?=$dtForumCategory->getFullURL()?>/edit-forum-category#AddForumCategory" class="btn btn-warning btn-circle" style="margin-left:10px" >
                                <i class="glyphicon glyphicon-wrench text-too table-forums-icon"> </i>
                            </a>

                        <?php endif;?>

                        </h3>
                    </a>

                    <div id="addTopicFormInlineStatus_<?=$dtForumCategory->sID?>_<?=$sFormIndex?>"  style="overflow: hidden"></div>
                    <div id="addTopicFormInline_<?=$dtForumCategory->sID?>_<?=$sFormIndex?>"> </div>

                </th>
                <th style="width: 25px"><i class="fa fa-comments-o table-forums-icon"  aria-hidden="true" ></i></th>
                <th style="width: 25px"><i class="fa fa-eye table-forums-icon" aria-hidden="true"></i></th>



            </tr>

            <tr class="anchor" id="TopicSubmissionTable_<?=$dtForumCategory->sID?>"> </tr>
            <?=$topicsContent?>