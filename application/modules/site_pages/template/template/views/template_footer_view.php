<footer class="main-footer" style="<?=$this->RightSideBar->getContentWrapperStyle()?>">
    <div class="main-footer-menu ">
        <?php
        $g_objFooterUserMenu->renderMenu();
        ?>
    </div>

    <div class="main-footer-copyright">
        <strong>Copyright &copy; 2017 <a href="http://bit-technologies.net">BIT TECHNOLOGIES</a></strong>
    </div>

    <div class="main-footer-logo">
        <a href="<?=base_url('')?>" ><img src='<?=base_url('theme/images/SkyHub-logo-small.png')?>' alt="SkyHub Logo" title="SkyHub connecting the world"> </a>
    </div>

</footer>