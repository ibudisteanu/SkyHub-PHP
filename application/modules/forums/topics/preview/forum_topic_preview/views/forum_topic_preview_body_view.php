<?php  $topicPreviewImage = $dtTopicPreview->objImagesComponent->getImageFirst(true);?>

<?php if ($topicPreviewImage != null) : ?>

    <?php if ($topicPreviewImage['type'] == 'icon') : ?>
        <ul class="media-list">
            <li class="media">
                <div class="pull-left">
                    <i class="<?=$topicPreviewImage['src']?>" style="font-size: 4em; "></i>
                </div>
                <div class="media-body">

                    <?=$this->load->view('forum_topic_preview_body_content_view')?>

                </div>
            </li>
            <!-- COMMENT SECTION - END-->
        </ul>
    <?php else: ?>
        <a href="<?=$topicPreviewImage['src']?>">
            <img class="table-forums-topic-image" src="<?=$topicPreviewImage['src']?>" <?=( (isset($topicPreviewImage['title']) && $topicPreviewImage['title'] != '') ? 'title="'.$topicPreviewImage['title'].'"  ':'') ?> <?=(((isset($topicPreviewImage['alt']))&&($topicPreviewImage['alt'] !='')) ? 'alt="'.$topicPreviewImage['alt'].'" ' : '') ?> align="left" width="25%"></img>
        </a>

        <?=$this->load->view('forum_topic_preview_body_content_view')?>

    <?php endif; ?>
<?php else : ?>
    <?=$this->load->view('forum_topic_preview_body_content_view')?>
<?php endif; ?>
