<!DOCTYPE html>
<html lang="<title><?php echo isset($g_sLanguage) ? $g_sLanguage : 'en' ; ?></title>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo isset($g_sTitle) ? $g_sTitle : WEBSITE_TITLE ; ?></title>

    <!-- Mobile Specific Metas
  ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?=base_url('assets/js/bootstrap.min.js')?>"></script>
    <!-- initialize jQuery Library -->
    <script type="text/javascript" src="<?php echo base_url('js/jquery.js')?>"></script>
    <!-- Bootstrap jQuery -->
    <script type="text/javascript" src="<?php echo base_url('js/bootstrap.min.js')?>"></script>

    <!-- CSS
  ================================================== -->
    <!-- Bootstrap -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css')?>" rel="stylesheet">
    <?php if (isset($g_sSpecialCSS))
        echo '<link href="'. base_url('css/'.$g_sSpecialCSS).'" rel="stylesheet">';
    ?>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css')?>">
    <!-- Elegant icon font -->
    <link rel="stylesheet" href="<?php echo base_url('css/line-icons.min.css')?>">

    <!-- Prettyphoto -->
    <link rel="stylesheet" href="<?php echo base_url('css/prettyPhoto.css')?>">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="<?php echo base_url('css/owl.carousel.css')?>">
    <link rel="stylesheet" href="<?php echo base_url('css/owl.theme.css')?>">
    <!-- Scrolling nav css -->
    <link rel="stylesheet" href="<?php echo base_url('css/scrolling-nav.css')?>">
    <!-- Template styles-->
    <link rel="stylesheet" href="<?php echo base_url('css/style.css')?>">
    <!-- Responsive styles-->
    <link rel="stylesheet" href="<?php echo base_url('css/responsive.css')?>">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body data-spy="scroll" data-target=".navbar-fixed-top" >

<a class="anchor" id="top"></a>