<?php
    $g_sActivePage='profile';
    if ($g_NavItem->bHeader) echo '<li class="header">'.$g_NavItem->sText.'</li>';
    else
    {
        if ((isset($g_sActivePage)) && (strtolower($g_NavItem->sText) == strtolower($g_sActivePage)))
            echo '<li class="active ' . (($g_NavItem->iLevel == 0) ? 'treeview">' : '">');
        else
            echo '<li' . (($g_NavItem->iLevel == 0) ? ' class="treeview">' : '>');

        $this->load->view('left_sidebar_nav_item_view');
    }

?>

