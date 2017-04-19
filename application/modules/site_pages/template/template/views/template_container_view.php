<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="<?=$this->RightSideBar->getContentWrapperStyle()?>">
    <!-- Content Header (Page header) -->

    <!-- Main content -->

    <!-- Main row -->


    <div class="<?=($bContainer==true ? 'container' : 'row')?>" style="margin-right: 0; margin-left: 0;">

        <?php
            modules::load('alerts/alert_general')->index();
        ?>

        <?php
            $this->ContentContainer->renderViewByOrder();
        ?>
    </div>

</div>
<!-- /.content-wrapper -->

