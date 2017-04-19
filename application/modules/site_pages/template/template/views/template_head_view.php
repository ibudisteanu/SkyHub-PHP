<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$this->MetaController->getLanguage()?>" lang="<?=$this->MetaController->getLanguage()?>">

<head lang="<?=$this->MetaController->getLanguage()?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="wot-verification" content="38429430c03d2575e5f6"/>

    <?= $this->MetaController->getRender(''); ?>
    <link rel="publisher" href="https://plus.google.com/115296534521205284807">

    <?php
        if (isset($g_sMETA)) echo $g_sMETA;
    ?>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- jQuery 2.2.0 -->
    <script src="<?= !defined('WEBSITE_OFFLINE') ? 'http://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js' : base_url('assets/js/jquery.min.js') ?>"></script>
    <!--<script src="<?=base_url("theme/plugins/jQuery/jQuery-2.2.0.min.js")?>"></script> -->
    <!-- Bootstrap 3.3.6 -->
    <script <?=!defined('WEBSITE_OFFLINE') ? 'src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"' : 'src="'.base_url('assets/js/bootstrap.js').'"'?>></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?=base_url('theme/bootstrap/xxs/bootstrap-patched.min.css')?>">
    <link rel="stylesheet" href="<?=base_url('theme/bootstrap/xxs/bootstrap-xxs-tn.min.css')?>">

    <?= (defined('WEBSITE_OFFLINE') && defined('WEBSITE_OFFLINE_DEBUG')) ? '<script src="'.base_url('theme/bootstrap/xxs/bsdebug.js').'"></script>' : ''?>

    <link rel="stylesheet" href="<?=$g_sThemeURL?>dist/css/style.css">

    <!-- Icons -->
    <link rel="stylesheet" href="<?=!defined('WEBSITE_OFFLINE') ? 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css' : base_url('assets/fonts/font-awesome-4.6.3/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?=!defined('WEBSITE_OFFLINE') ? 'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"' : base_url('assets/fonts/ionicons.min.css')?>">
    <link rel="apple-touch-icon" sizes="57x57" href="<?=$g_sThemeURL?>images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?=$g_sThemeURL?>images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?=$g_sThemeURL?>images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?=$g_sThemeURL?>images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?=$g_sThemeURL?>images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?=$g_sThemeURL?>images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?=$g_sThemeURL?>images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?=$g_sThemeURL?>images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=$g_sThemeURL?>images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?=$g_sThemeURL?>images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=$g_sThemeURL?>images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?=$g_sThemeURL?>images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=$g_sThemeURL?>images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?=$g_sThemeURL?>images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?=$g_sThemeURL?>images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- jvectormap
    <link rel="stylesheet" href="<?=$g_sThemeURL?>plugins/jvectormap/jquery-jvectormap-1.2.2.css">-->
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=$g_sThemeURL?>dist/css/AdminLTE.css">
    <link rel="stylesheet" href="<?=base_url('assets/css/skyhub.css')?>">
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="<?=$g_sThemeURL?>dist/css/skins/_all-skins.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?php
        if (!((isset($g_bHideAnalytics))&&($g_bHideAnalytics)))
            $this->load->view('meta/analytics_tracking');
    ?>

    <link rel="publisher" href="https://plus.google.com/115296534521205284807">

</head>

<body class="<?= ($g_bSideBarDisabled == true) ?  'skin-blue fixed  sidebar-collapse" data-target="#scrollspy" data-spy="scroll"' : 'hold-transition skin-blue sidebar-mini fixed  wysihtml5-supported fixed '.($this->RightSideBar->bVisible ? 'control-sidebar-open sidebar-collapse' : '') .'"' ?> >
<div class="wrapper" style="">