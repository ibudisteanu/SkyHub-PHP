<li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-envelope-o"></i>
            <?php if (defined('WEBSITE_OFFLINE')) : ?>
                <span class="label label-success">1</span>
            <?php endif;?>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have 0 messages</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <?php if (defined('WEBSITE_OFFLINE')) : ?>
                    <li><!-- start message -->
                        <a href="#">
                            <div class="pull-left">
                                <img src="<?=$g_sThemeURL?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                            </div>
                            <h4>
                                Support Team
                                <small><i class="fa fa-clock-o"></i> 5 mins</small>
                            </h4>
                            <p>Welcome to SkyHub</p>
                        </a>
                    </li>
                    <!-- end message -->
                    <li>
                        <a href="#">
                            <div class="pull-left">
                                <img src="<?=$g_sThemeURL?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                            </div>
                            <h4>
                                Support Team
                                <small><i class="fa fa-clock-o"></i> 2 hours</small>
                            </h4>
                            <p>TEST MESSAGE</p>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <div class="pull-left">
                                <img src="<?=$g_sThemeURL?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                            </div>
                            <h4>
                                Support Team
                                <small><i class="fa fa-clock-o"></i> Today</small>
                            </h4>
                            <p>TEST MESSAGE</p>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <div class="pull-left">
                                <img src="<?=$g_sThemeURL?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                            </div>
                            <h4>
                                Support Team
                                <small><i class="fa fa-clock-o"></i> Yesterday</small>
                            </h4>
                            <p>TEST MESSAGE</p>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <div class="pull-left">
                                <img src="<?=$g_sThemeURL?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                            </div>
                            <h4>
                                Support Team
                                <small><i class="fa fa-clock-o"></i> 2 days</small>
                            </h4>
                            <p>TEST MESSAGE</p>
                        </a>
                    </li>
                <?php endif ;?>
            </ul>
        </li>
        <li class="footer"><a href="#">See All Messages</a></li>
    </ul>
</li>