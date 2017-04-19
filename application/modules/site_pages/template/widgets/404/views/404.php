<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="description" content="404 not found error">
<meta name="author" content="SkyHub Web Platform">

<link rel="stylesheet" href="<?=base_url("assets")?>/404_files/main.css" type="text/css" media="screen, projection"> <!-- main stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="<?=base_url("assets")?>/404_files/tipsy.css"> <!-- Tipsy implementation -->

<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="css/ie7.css" />
<![endif]-->

<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script> <!-- jQuery implementation -->
<script type="text/javascript" src="<?=base_url("assets")?>/404_files/custom-scripts.js"></script><!-- All of my custom scripts -->
<script type="text/javascript" src="<?=base_url("assets")?>/404_files/jquery.tipsy.js"></script> <!-- Tipsy -->

<script type="text/javascript">

$(document).ready(function(){
			
	universalPreloader();
						   
});

$(window).load(function(){

	//remove Universal Preloader
	universalPreloaderRemove();
	
	rotate();
    dogRun();
	dogTalk();

	//Tipsy implementation
	$('.with-tooltip').tipsy({gravity: $.fn.tipsy.autoNS});
						   
});

</script>


<title>404 - Not found</title>
</head>

<body>

<!-- Universal preloader -->

<!-- Universal preloader -->

<div id="wrapper">
<!-- 404 graphic -->
	<div class="graphic">
        <img src="<?=base_url("assets")?>/404_files/404.png" alt="404">
    </div>

<!-- 404 graphic -->

<!-- Text, search form and menu -->
<div class="top-left">
    <!-- Not found text -->
    	<div class="not-found-text">
        	<h1 class="not-found-text">File not found <?=$_SERVER['REQUEST_URI']?>, sorry!</h1>
        </div>
    <!-- Not found text -->

    <!-- search form -->
    <div class="search">
    	<form name="search" method="get" action="#">
            <input type="text" name="search" value="Search ...">
            <input class="with-tooltip" type="submit" name="submit" value="" original-title="Search!">
        </form>
    </div>
    <!-- search form -->

    <!-- top menu -->
    <div class="top-menu">
    	<a href="<?=base_url("")?>" class="with-tooltip" original-title="Return to the home page">Home</a> | <a href="<?=base_url("uploads/sitemap/sitemap.xml")?>" class="with-tooltip" original-title="Navigate through our sitemap">Sitemap</a> | <a href="<?=base_url("contact")?>" class="with-tooltip" original-title="Contact us!">Contact</a> | <a href="#" class="with-tooltip" original-title="Request additional help">Help</a>
    </div>
    <!-- top menu -->
</div>
<!-- Text, search form and menu -->
    <div class="SkyHubLogo">
        <img src="<?=base_url("theme/images/SkyHub-logo.png")?>" alt="404">
    </div>
<!-- planet at the bottom -->
	<div class="planet">
        <div class="dog-wrapper">
        <!-- dog running -->
            <div class="dog" style="background-position: -80px -2px;"></div>
        <!-- dog running -->
            
        <!-- dog bubble talking
            <div class="dog-bubble" style="opacity: 1; bottom: 10px;"><p>
                    Are you lost, bud? No worries, I'm an excellent guide!
                </p></div>
            
            <!-- The dog bubble rotates these -->

            <div class="bubble-options">
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    Are you lost, bud? No worries, I'm an excellent guide!
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    <br>
                    Arf! Arf!
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    <br>
                    Don't worry! I'm on it!
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    I wish I had a cookie<br><img style="margin-top:8px" src="<?=base_url("assets")?>/404_files/cookie.png" alt="cookie">
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    <br>
                    Geez! This is pretty tiresome!
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    <br>
                    Am I getting close?
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    Or am I just going in circles? Nah...
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    <br>
                    OK, I'm officially lost now...
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    I think I saw a <br><img style="margin-top:8px" src="<?=base_url("assets")?>/404_files/cat.png" alt="cat">
                </p>
                <p class="dog-bubble" style="opacity: 1; bottom: 10px;">
                    What are we supposed to be looking for, anyway? @_@
                </p>
            </div>
            <!-- The dog bubble rotates these -->
        <!-- dog bubble talking -->
        </div>

        <!-- planet image -->
        <img src="<?=base_url("assets")?>/404_files/planet.png" alt="planet" style="transform: rotate(-7068.6deg);">
        <!-- planet image -->
    </div>
<!-- planet at the bottom -->
</div>



</body></html>