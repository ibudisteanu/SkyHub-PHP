<a id="popupAuthenticationModalTrigger" href="#popupAuthenticationModal"></a>
<div id="popupAuthenticationModal" class="popupContainer" style="display:none;">
    <header class="popupHeader">
        <span class="popupTitle">LOGIN to <?=WEBSITE_TITLE?></span>
        <span id="btnPopupAuthenticationModalClose" class="modal_close"><i class="fa fa-times"></i></span>
    </header>

    <section class="popupBody">
        <!-- Social Login -->
        <div class="popupLogin">

            <?php
            echo modules::load('auth_site/login')->index('form','');
            ?>

            <div class="popupActionsBtns" style="padding-top:15px; padding-bottom: 6px">

                <div class="col-md-12 center-block text-center">
                    <button id="btnPopupAuthenticationRegister" class="btn btn-primary">
                        Go to REGISTRATION <i class="fa fa-angle-double-right"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Register Form -->
        <div class="popupRegister">

            <?php
                echo modules::load('auth_site/registration')->index('form','');
            ?>

            <div class="popupActionsBtns" style="padding-top:10px; padding-bottom: 6px; ">
                <div class="col-md-12 center-block text-center">
                    <button id="btnPopupAuthenticationLogin" class="btn btn-danger">
                        <i class="fa fa-angle-double-left"> Back to LOGIN</i>
                    </button>
                <div>
            </div>

        </div>
    </section>
</div>