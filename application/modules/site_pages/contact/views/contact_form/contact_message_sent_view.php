<div class="heading-inner text-center" >
    <h2 class="sec-title">Your message was sent successfully to <span><?= WEBSITE_NAME?></span></h2>

    <div class="copyright">
        <p>A message with your input has been sent to <?= WEBSITE_NAME?>. We will respond you as soon as possible.</p>
    </div>

</div>

<?php
    $this->AlertsContainer->renderViewByName('g_msgContactSuccess');
    $this->AlertsContainer->renderViewByName('g_msgContactError');
?>

