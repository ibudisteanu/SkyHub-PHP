<!-- User Account: style can be found in dropdown.less -->
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <img src="<?=$this->MyUser->getCustomAvatarImage(50)?>" class="user-image" alt="<?=$this->MyUser->getFullName()?>">
        <span class="hidden-xs hidden-xxs hidden-tn"><?= $this->MyUser->sFirstName. ' ('.$this->MyUser->sUserName. ')'?></span>
    </a>
    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <img src="<?=$this->MyUser->getCustomAvatarImage(300)?>" class="user-big-avatar img-circle" alt="<?=$this->MyUser->getFullName()?>">

            <p>
                <?= $this->MyUser->getFullName() . ' ('.$this->MyUser->sUserName. ')'?>
                <small>Member since <?=$this->MyUser->getCreationDateString()?></small>
            </p>
        </li>
        <!-- Menu Body -->
        <li class="user-body">
            <div class="row">
                <div class="col-xs-4 text-center">
                    <a href="#">Forums</a>
                </div>
                <div class="col-xs-4 text-center">
                    <a href="#">Posts</a>
                </div>
                <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                </div>
            </div>
            <!-- /.row -->
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <a href="<?=base_url('profile/'.$this->MyUser->getUserLink())?>" class="btn btn-default btn-flat">Profile</a>
            </div>
            <div class="pull-right">
                <a href="<?=base_url('logout')?>" class="btn btn-default btn-flat">Sign out</a>
            </div>
        </li>
    </ul>
</li>
