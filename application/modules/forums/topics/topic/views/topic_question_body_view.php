<div id="articleContent" class="container-fluid topic-question-body">

    <?php  $topicImage = $dtTopic->objImagesComponent->getImageFirst(true);  ?>

    <?php if ($topicImage['type'] == 'icon') : ?>
        <ul class="media-list">
            <li class="media">
                <div class="pull-left">
                    <?php if ($topicImage['src'] != '') : ?>
                        <i class="<?=$topicImage['src']?>" style="font-size: 5em; padding-right:5px"></i>
                    <?php endif; ?>
                </div>
                <div class="media-body">
                    <?=$dtTopic->getBodyCodeRendered()?>
                </div>
            </li>
            <!-- COMMENT SECTION - END-->
        </ul>
    <?php else: ?>

        <?php if ($topicImage['src'] != '') : ?>
            <a href="<?=$topicImage['src']?>">
                <img src = "<?=$topicImage['src']?>" <?=( (isset($topicImage['title']) && $topicImage['title'] != '') ? 'title="'.$topicImage['title'].'"  ':'') ?> <?=(((isset($topicImage['alt']))&&($topicImage['alt'] !='')) ? 'alt="'.$topicImage['alt'].'" ' : '') ?> align="left" width="40%" style="background-color: white; padding: 0 10px 10px 0;"></img>
            </a>
        <?php endif ;?>

        <?=$dtTopic->getBodyCodeRendered()?>

    <?php endif; ?>

</div>