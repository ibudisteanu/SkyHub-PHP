<div class="container">
    <h1>Edit Profile - <?=$g_User->getFullName() ?></h1>
    <hr>

    <div class="row">
        <!-- left column -->
        <div class="col-md-3">

            <?php
                $this->load->view('profile/edit_profile/upload_avatar_form');
            ?>
        </div>

        <div class="col-md-8 personal-info">
            <!-- edit profile form column -->
            <div class="box box-info">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>

                    <h3 class="box-title">Edit Your Account Details</h3>
                </div>

                <?php
                    $this->load->view('profile/edit_profile/edit_profile_form');
                ?>
            </div>



            <!-- change password form column -->
            <div class="box box-info">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>

                    <h3 class="box-title">Change your password</h3>
                </div>

                <?php
                    $this->load->view('profile/edit_profile/change_password_form');
                ?>
            </div>

        </div>



    </div>
</div>
<hr>