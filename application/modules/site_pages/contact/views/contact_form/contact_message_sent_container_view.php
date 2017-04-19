<!-- Section Benifit start -->
<a class="anchor" id="Contact"></a>

<section id="contact2" >

    <div class="container" style="padding-bottom: 150px">
        <div class="row">

            <div class="heading-inner text-center" style="padding-bottom: 50px">
                <h2 class="sec-title">Your message was sent successfully to <span><?= WEBSITE_NAME?></span></h2>

                <div class="copyright">
                    <p>A message with your input has been sent to <?= WEBSITE_NAME?>. We will respond you as soon as possible.</p>
                </div>

            </div>

            <?php
                $this->AlertsContainer->renderViewByName('g_msgContactSuccess');
                $this->AlertsContainer->renderViewByName('g_msgContactError');
            ?>


    </div>

</section>


<!-- Section Benifit End -->