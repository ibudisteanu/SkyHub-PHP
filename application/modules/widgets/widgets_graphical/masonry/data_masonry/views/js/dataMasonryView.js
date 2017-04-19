<script>
(function ($) {
    var $container = $(".masonry-container");

        $container.masonry({
            columnWidth: ".item",
            itemSelector: ".item"
        });


        $container.imagesLoaded( function() {
            $container.masonry('layout');
        });

        // layout Masonry after each image loads
        $container.imagesLoaded().progress( function() {
            $container.masonry('layout');
        })

}(jQuery));
//# sourceURL=pen.js
</script>