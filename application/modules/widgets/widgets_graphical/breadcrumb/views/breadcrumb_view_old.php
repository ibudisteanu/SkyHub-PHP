<div class="page-header page-heading" style="margin : 0px 60px 0px 60px; ">
    <h1 class="pull-left" style="margin-top: 10px; margin-bottom: 0px;">Forums</h1>
    <ol class="breadcrumb pull-right where-am-i" style="margin-bottom: 0px;">

        <?php
        for ($index=0; $index < count($breadCrumbArray); $index++)
        {
            $element = $breadCrumbArray[$index]; $sActive='';
            if ($index == count($breadCrumbArray)-1) $sActive = 'class="active"';

            echo '<li '.$sActive.'>';
            if ($element['url'] != '') echo '<a href="'.$element['url'].'">';
            echo $element['name'];
            if ($element['url'] != '') echo '</a>';
            echo '</li>';
        }
        ?>
    </ol>
    <div class="clearfix"></div>
</div>