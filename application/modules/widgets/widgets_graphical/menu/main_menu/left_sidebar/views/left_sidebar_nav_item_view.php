    <a href="<?=$g_NavItem->sLink?>">
        <i class="<?=$g_NavItem->sImg?>"></i>
        <span><?=$g_NavItem->sText?></span>
        <?php
            if (count($g_NavItem->arrSubItems) > 0)
                echo '<i class="fa fa-angle-left pull-right"></i>';

            if ($g_NavItem->sLabelAttachedValue != '')
                echo '<span class="label '.$g_NavItem->sLabelAttachedType.'">'.$g_NavItem->sLabelAttachedValue.'</span>';
        ?>
    </a>
    <?php
        if (count($g_NavItem->arrSubItems) > 0)
        {
            echo '<ul class="treeview-menu">';

            $MenuSubItems = $g_NavItem->arrSubItems;

            foreach ($MenuSubItems as $SubItem)
            {
                $this->load->vars(array('g_NavItem' => $SubItem));
                $this->load->view('left_sidebar_nav_item_header_view');
            }

            echo '</ul>';
        }
    ?>
</li>