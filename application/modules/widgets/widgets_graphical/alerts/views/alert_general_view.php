<?php
    $sContent = '';

    $sContent .= $this->AlertsContainer->renderViewByName('g_msgGeneralSuccess','center');
    $sContent .= $this->AlertsContainer->renderViewByName('g_msgGeneralError','center');
    $sContent .= $this->AlertsContainer->renderViewByName('g_msgGeneralWarning','center');
?>

<?php
    if ($sContent != '')
    {
        echo '<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 feature" style="margin-left: auto ;margin-right: auto ; padding=15px 0 0 0; border=0px" data-wow-delay=".3s" >';
        echo $sContent;
        echo '</div>';
    }
?>