<!-- Section features start -->
<a class="anchor" id="About"></a>

<div class="black-area wow fadeInUp">
    <div class="container">
        <div class="col-md-12 col-sm-12 col-xs-12" data-wow-delay=".3s" style="align-content: center; padding-top:20px; padding-bottom: 0;">
            <div class="heading-inner text-center" >
                <h2>About <strong><?= WEBSITE_NAME ?></strong></h2>

                <div class="copyright">
                    <p><?= WEBSITE_NAME?> is a Social Revenue Sharing Forum Platform where anybody can connect, discover and change the world through this amazing new web platform.</p>
                    <p>This new platform has been developed in <strong>Romania</strong> and <strong>Mountain View</strong>.</p>

                    <p><strong>We are not responsible for the content of the users. The responsibility of the content is entirely of the users.</strong></p>


                    <p>The founder of <?= WEBSITE_NAME ?> is <strong><a href="http://budisteanu.net/" >Alexandru Ionut Budisteanu</a></strong>, the CEO of <strong><a href="http://bit-technologies.net/">BIT TECHNOLOGIES</a></strong></p>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="map_container">
    <div id="mapCanvas" >
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcq4aUHrd-QNwu7yZqYU5-N1jc5AFXubQ"></script>

    </div>
</div>

<!-- Section features end -->

<!-- About Location Script end -->
<script type="text/javascript">

    var map;

    $(window).resize(function () {
        var h = $(window).height(),
            offsetTop = 105; // Calculate the top offset

        $('#mapCanvas').css('height', 500);
    }).resize();

    function googleMapsInitialize() {
        var location = new google.maps.LatLng(45.107422, 24.377496);
        var myOptions = {
            zoom: 5,
            center: new google.maps.LatLng(48.107422, 24.377496),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("mapCanvas"),
            myOptions);

        var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<h3 id="firstHeading" class="firstHeading" style="margin-top:0; text-align:center"><img src="<?=base_url("theme/images/SkyHub-logo-small.png")?>" alt="<?=WEBSITE_NAME?>" title="<?=WEBSITE_NAME?>"></h3>'+
            '<div id="bodyContent">'+
            '<p><b><?=WEBSITE_NAME?></b> is developed by <a href="http://bit-technologies.net/">BIT TECHNOLOGIES</a></b></p>'+
            '<p><b><?=WEBSITE_NAME?></b> is based in <b>Ramnicu Valcea, Romania</b></p>'+
            '<p>Ferdinand Street, No 28, Valcea, 240156, ROMANIA' +
            '</p>'+
            '</div>';

        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });

        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: '<?=WEBSITE_NAME?> at NASA'
        });
        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });

        infowindow.open(map, marker);

    }

    var center;
    function calculateCenter() {
        center = map.getCenter();
    }

    /*
    google.maps.event.addDomListener(map, 'idle', function() {
        calculateCenter();
    });
    google.maps.event.addDomListener(window, 'resize', function() {
        map.setPosition(location);
        //map.setCenter(center);
    });*/

    google.maps.event.addDomListener(window, "load", googleMapsInitialize);

</script>
