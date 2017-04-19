<?php

require_once __DIR__.'/../CrawlerInfo.php';

class CrawlerData_SkyHub_info extends CrawlerInfo
{
    public $sWebsite = 'http://skyhub.me/';
    public $arrWebsite = array('skyhub.me');

    public $bRTrimSharp = false; // Remove all string after #

    public $arrPages = [
        ["link"=>"http://skyhub.me/","website"],
    ];

    public $arrUnFollowPages = [
        ["link"=>"http://skyhub.me/r"],
        ["link"=>"http://skyhub.me/#control-sidebar-settings-tab"],
        ["link"=>"http://skyhub.me/javascript:void(0)"],
        ["link"=>"http://skyhub.me/r.php"],
        ["remove"=>"add-topic/#AddTopic"],
        ["contains"=>"/signup/google"],
        ["contains"=>"/signup/twitter"],
        ["contains"=>"/signup/linkedin"],
        ["contains"=>"/mailto:"],
        ["contains"=>"/uas/"],
        ["contains"=>"/secure/settings"],
    ];



}