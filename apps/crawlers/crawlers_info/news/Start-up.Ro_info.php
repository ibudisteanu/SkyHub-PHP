<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_StartupRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://start-up.ro/';
    public $sWebsiteName = 'Start-up Ro';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('start-up.ro');

    public $arrPages = [
        ["link"=>"https://start-up.ro/erik-barna-de-la-petrosani-la-propria-firma-de-it-listata-pe-bursa/","link"],
        ["link"=>"https://start-up.ro/","link"]
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagName"=>"div",
                    "tagClass" => "article-wrapper article-view-default bg-white"
                ]
            ,
            "short-description" =>
                [
                    "tagName"=>"div",
                    "class" => "article-lead"
                ],
            "content"=>
                [
                    "tagName"=>"div",
                    "tagClass"=>"article-text",
                    "remove-links"=>true,
                ]
            ,
            "author"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"ul",
                            "tagClass"=>"article-authors list-unstyled list-inline"
                        ],
                    "tagName"=>"li",
                ]
            ,
            "date"=>
                [
                    "tagName"=>"li",
                    "tagClass" => "article-date",
                ]
            ,
            "image"=>
                [
                    "image-preview-alt"=>
                        [
                            "tagClass"=>"img-responsive",
                            "attribute_return"=>"alt",
                        ]
                ]
        ];
}