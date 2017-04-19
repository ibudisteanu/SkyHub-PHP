<!-- Header start -->
<header id="header" role="banner" >
    <nav class="navbar navbar-default navbar-fixed-top"  id="tf-menu">
        <div class="container">
            <div class="row">
                <!-- Logo start -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-brand">
                        <a href="<?= base_url('')?>" class="page-scroll">
                            <img class="img-responsive" src="<?= base_url('images/logo1.png')?>" alt="logo">
                        </a>
                    </div>
                </div><!--/ Logo end -->
                <div class="collapse navbar-collapse clearfix navMenu" role="navigation">
                    <ul class="nav navbar-nav navbar-right">
                        <?php foreach($g_arrNavigationMenu as $nav )
                        {
                            //Drop Down List
                            if (is_array($nav[1]))
                            {
                                echo '<li >';
                                echo '<a class="dropdown-toggle" data-toggle="dropdown" href="'.$nav[1][0][1].'">'.$nav[1][0][0].' <b class="caret"></b></a>';
                                    echo '<ul class="dropdown-menu">';

                                    $MenuSubItems = $nav[1];

                                    //<li class="divider"></li>
                                    foreach ($MenuSubItems as $navSubItem)
                                    {
                                        if ($navSubItem[0]=='divider')
                                            echo '<li class="divider"></li>';
                                        else
                                            echo '<li><a href="'.$navSubItem[1].'">'.$navSubItem[0].'</a></li>';
                                    }


                                    echo '</ul>';
                                echo '</li>';
                            } else {
                                if ((isset($g_sActivePage)) && (strtolower($nav[0]) == strtolower($g_sActivePage)))
                                    echo '<li class="active">';
                                else
                                    echo '<li>';

                                echo '<a class="page-scroll" href="' . $nav[1] . '">' . $nav[0] . '</a></li>';
                            }
                        }?>

                    </ul>
                </div><!--/ Navigation end -->
            </div><!--/ Row end -->
        </div><!--/ Container end -->
    </nav>
</header><!--/ Header end -->
<!-- END MAIN NAV -->