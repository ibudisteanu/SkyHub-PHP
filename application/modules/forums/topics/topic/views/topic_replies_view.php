<div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp replies-container">
    <!-- The time line -->
    <ul class="timeline" >
        <!-- timeline time label -->
        <li class="time-label">
                  <span class="bg-red"><?=$dtTopic->getCreationDateString()?></span>
        </li>

        <?php
            if (($dtRepliesContainer != null)&&(isset($dtRepliesContainer->arrChildren)))
            {
                $DisplayAdsAlgorithmController = modules::load('ads/display_ads_algorithm_controller');
                $DisplayAdsAlgorithmController->initializeAdsAlgorithm($dtRepliesContainer->arrChildrenCount);

                foreach ($dtRepliesContainer->arrChildren as $Reply)
                    echo $this->ViewReplyController->renderReply($Reply, $DisplayAdsAlgorithmController, true);
            }
        ?>

        <li id="repliesNewContainer<?=$dtTopic->sID?>" style="overflow: hidden"> </li>

        <!-- END timeline item -->
        <li style="padding-bottom: 20px">
            <i class="fa fa-clock-o bg-gray"></i>
        </li>
    </ul>
</div>