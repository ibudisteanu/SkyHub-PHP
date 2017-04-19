<?php
    //Drop Down List
    if (count($g_NavItem->arrSubItems)>0)
    {
        echo '<li>';

        echo '<a class="dropdown-toggle" data-toggle="dropdown" '.($g_NavItem->sOnClick != '' ? "onClick='$g_NavItem->sOnClick'" : '').($g_NavItem->sLink != '' ? "href='$g_NavItem->sLink. '>" : '').'>'.$g_NavItem->sText.' <b class="caret"></b></a>';
        echo '<ul class="dropdown-menu">';

        $MenuSubItems = $g_NavItem->arrSubItems;

        //<li class="divider"></li>
        foreach ($MenuSubItems as $navSubItem)
        {
            if ($navSubItem->sText=='divider')
                echo '<li class="divider"></li>';
            else
                echo '<li><a href="'.$navSubItem->sLink.'">'.$navSubItem->sText.'</a></li>';
        }


        echo '</ul>';
        echo '</li>';
    } else {
        if ((isset($g_sActivePage)) && (strtolower($g_NavItem->sText) == strtolower($g_sActivePage)))
            echo '<li class="active">';
        else
            echo '<li>';


        if ($g_NavItem->sText == 'Search')
        {
            echo $this->load->view('nav_item_search_view',null, TRUE);
        } else
        {
            echo '<a class="page-scroll" '.($g_NavItem->sOnClick != '' ? "onClick='$g_NavItem->sOnClick'" : '').($g_NavItem->sLink != '' ? "href='$g_NavItem->sLink'>" : '') . $g_NavItem->sText . '</a>';
        }




        echo '</li>';
    }

?>