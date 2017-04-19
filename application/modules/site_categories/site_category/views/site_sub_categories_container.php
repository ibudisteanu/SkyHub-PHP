<?php
    $sContent = $this->view('site_sub_categories',null, TRUE);
?>

<?php if ($sContent != '') : ?>
<!-- Section features start -->
<div class="col-md-12 col-sm-12 col-xs-12 feature " style="margin-left: auto;margin-right: auto; padding:20px 0 13px 0; border=0px" data-wow-delay=".3s" >

    <div class="container">
        <?=$sContent?>
    </div>

</div>
<!-- Section features end -->

<?php endif ; ?>