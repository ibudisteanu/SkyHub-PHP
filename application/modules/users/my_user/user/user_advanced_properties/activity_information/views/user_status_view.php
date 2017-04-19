<?php
switch ($User->ActivityInformation->getUserStatus())
{
    case 0:  //statusNone
        echo 'shit';
        break;
    case 1: //statusOnline
        echo '<img class="avatar-'.$styleClass.'-circle" src="'.base_url('theme/assets/images/user/online.png').'" alt="online">';
        break;
    case 2: //statusAway
        echo '<img class="avatar-'.$styleClass.'-circle" src="'.base_url('theme/assets/images/user/away.png').'" alt="away">';
        break;
    case 3: //statusOffline
        echo '<img class="avatar-'.$styleClass.'-circle" src="'.base_url('theme/assets/images/user/offline.png').'" alt="offline">';
        break;
}