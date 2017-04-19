<header class="main-header" >

    <!-- Logo -->
    <div class="navbar-header">

        <a href="<?=base_url('')?>" class="logo" >
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img src="<?=$g_sThemeURL?>images/SkyHub-logo-mini.png" title="<?=$g_sTitle?>" alt="<?=$g_sTitle?>" style="margin-top:-5px" ></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img src="<?=$g_sThemeURL?>images/SkyHub-logo-small.png" title="<?=$g_sTitle?>" alt="<?=$g_sTitle?>" style="margin-top:-5px" ></span>

        </a>

    </div>

    <!-- Header Navbar: style can be found in h/eader.less -->
    <nav class="navbar navbar-static-top">
        <!--<div class="container">-->
            <!-- Sidebar toggle button-->

            <?php
            if (!$g_bSideBarDisabled)
                echo '
                       <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                          <span class="sr-only">Toggle navigation</span>
                       </a>';
            ?>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            </div>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu" <?= (!$this->MyUser->bLogged ? 'style="right:0"' : '') ?>>

                <ul class="nav navbar-nav">

                    <?php
                        $g_objNavigationNavMenu->renderMenu();
                    ?>

                    <!-- Messages: style can be found in dropdown.less-->
                    <?php
                        $g_objUserMenu->renderMenu();
                        $g_objUserMenu->renderProfileMenu();
                    ?>

                </ul>

            </div>
         <!--</div>-->
    </nav>
</header>