<!-- Notifications: style can be found in dropdown.less -->
<li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" onClick="openNotificationsWindow(this)">
        <i class="fa fa-bell-o"></i>

        <span id="userNotificationsLabelSpan" class="label label-warning" <?=$iNewNotificationsCount == 0 ? 'style="display:none"' : ''?> ><?=$iNewNotificationsCount?></span>

    </a>
    <ul class="dropdown-menu">

        <li id="userNotificationsLabelWindow" class="header">You have <b><?=$iNewNotificationsCount?></b> new notifications</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul id="userNotificationsContentList" class="menu">

                <?=$newNotificationsContent?>

            </ul>
        </li>
        <li class="footer"><a href="#">View all</a></li>
    </ul>
</li>