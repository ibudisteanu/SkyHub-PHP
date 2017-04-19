<?php  if ($User == null) return?>

<a href="<?=base_url('profile/'.$User->getUserLink())?>">
    <?=$bShowUserName ? '<p style="text-decoration: none;">'.$User->getFullName().'</p>' : ''?>
    <img class="avatar-<?=$styleClass?>-image" src="<?=$User->getCustomAvatarImage(50)?>"  alt="<?=$User->sName?>">
</a>

<?=
    $this->load->view('activity_information/user_status_view.php',$data,true)
?>