  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="position: relative; overflow: hidden; width: auto; height: 100%;">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $this->MyUser->getCustomAvatarImage(50)?>" class="img" alt="<?=$this->MyUser->getFullName()?>" title="<?= $this->MyUser->getFullName()?>">
            </div>
            <div class="pull-left info">
                <p><?= $this->MyUser->getFullName()?></p>
                <a href="<?=base_url("profile/".$this->MyUser->getUserLink())?>"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
            <a href="<?=base_url($this->MyUser->getUserLink())?>">
            </a>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>


            <?php
                foreach ($this->navMenu as $menuItem)
                {
                    $this->load->vars(array('g_NavItem' => $menuItem));
                    $this->load->view('left_sidebar/left_sidebar_nav_item_header_view');
                }
            ?>


        </ul>
    </section>
    <!-- /.sidebar -->
  </aside>