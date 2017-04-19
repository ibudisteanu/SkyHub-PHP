<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
abstract class TUserRole
{
    const notLogged=0;
    const User = 1;
    const Admin = 77;
    const SuperAdmin = 666;

    const BotUser = 33;

    public static function checkUserRights($desiredRole)
    {
        $CI = &get_instance();
        $CI->load->model('./modules/users/my_user/session_user/models/MyUser',null,'MyUser');

        return TUserRole::checkCompatibility($CI->MyUser,$desiredRole);
    }

    public static function checkCompatibility($user, $desiredRole)
    {
        $currentRole  = $user->getUserRole();

        switch ($desiredRole)
        {
            case TUserRole::notLogged :
                return true;
                break;
            case TUserRole::User :
                return (($currentRole == TUserRole::User)||($currentRole == TUserRole::BotUser)||($currentRole == TUserRole::Admin) || ($currentRole == TUserRole::SuperAdmin)) ;
                break;
            case TUserRole::Admin :
                return (($currentRole == TUserRole::Admin)||($currentRole == TUserRole::SuperAdmin));
                break;
            case TUserRole::SuperAdmin :
                return ($currentRole == TUserRole::SuperAdmin);
                break;
            case TUserRole::BotUser:
                return ($currentRole == TUserRole::BotUser) ;
                break;
        }

    }
}