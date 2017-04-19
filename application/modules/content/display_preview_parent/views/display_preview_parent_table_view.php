<?=$bMasonryItem ? '<div class="item" style="left: 0px; top: 0px; width: 100%; ">' : '' ?>

<table class="table table-hover table-forums parent-table">
    <tbody>
        <?php $sFormIndex = ((string)rand(0,100000000).(string)rand(0,100000000)) ?>
        <tr id="topicsContainer_<?=$sParentId?>_<?=$sFormIndex?>" >
            <th>
                <a href="<?=$sFullURL?>">
                    <h3>
                        <?php

                        if ($sImage != '') {
                            if ($objParent->isIcon($sImage))
                                echo '<i class="' . $sImage . ' table-forums-icon" style="padding-right:5px"></i>';
                            else
                                if ($sImage != '') echo '<img src="' . $sImage . '" alt="' . $sName . '" style="max-width: 42px; max-height: 42px;">';
                        }

                        echo $sName;
                        ?>

                        <?=$this->AddTopicInlineController->renderTopicAddButton($sParentId, $sFormIndex, "topic-preview-table")?>

                        <?php
                        if (($objParent != null)&&($objParent->checkOwnership())) : ?>

                            <a href="<?=$objParent->getFullURL()?>/edit-forum-category#AddForumCategory" class="btn btn-warning btn-circle" style="margin-left:10px" >
                                <i class="glyphicon glyphicon-wrench text-too table-forums-icon"> </i>
                            </a>

                        <?php endif;?>

                    </h3>
                </a>

                <div id="addTopicFormInlineStatus_<?=$sParentId?>_<?=$sFormIndex?>"  style="overflow: hidden"></div>
                <div id="addTopicFormInline_<?=$sParentId?>_<?=$sFormIndex?>"> </div>

            </th>
            <th><i class="fa fa-comments-o table-forums-icon"  aria-hidden="true" ></i></th>
            <th><i class="fa fa-eye table-forums-icon" aria-hidden="true"></i></th>



        </tr>

        <tr class="anchor" id="TopicSubmissionTable_<?=$sParentId?>"> </tr>

        <?=$topicsContent?>

    </tbody>
</table>

<?=$bMasonryItem ? '</div> ' : ''?>

<div style="margin-bottom:15px"></div>


<?php
    $this->AlertsContainer->renderViewByName('g_msgAddForumCategorySuccess');
    $this->AlertsContainer->renderViewByName('g_msgAddForumCategoryError');
    $this->AlertsContainer->renderViewByName('g_msgAddForumCategoryWarning');
?>
