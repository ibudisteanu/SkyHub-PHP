<?php if (!defined('ADS_ENABLED')) return ?>
<aside id="rightSideBar" class="hidden-xxs hidden-tn control-sidebar control-sidebar-dark control-sidebar-open" style=" max-height: none; overflow:hidden !important; position: fixed; height: auto; <?=isset($iWidth) ? 'width:'.$iWidth.'px;' : '' ?> ">
    <!-- Create the tabs -->

    <!-- Tab panes -->
    <div class="tab-content" style="padding: 1px; padding-right: 0; overflow: hidden !important;">
        <!-- Home tab content -->

        <div id="control-sidebar-theme-demo-options-tab" class="tab-pane active">
                <!--<h4 class="control-sidebar-heading" style="text-align: center">Ads</h4>-->

                <?=
                    modules::load('ads/display_ads_algorithm_controller')->renderRightSidebarAds(false);
                    modules::load('ads/display_ads_algorithm_controller')->renderRightSidebarAds(false);
                ?>

        </div>
        <!-- /.tab-pane -->


        <!-- /.tab-pane -->
    </div>
</aside>
