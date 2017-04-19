<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_FinantareRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://www.finantare.ro/';
    public $sWebsiteName = 'Finantare.ro';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('www.finantare.ro');

    public $arrPages = [
        ["link"=>"http://www.finantare.ro/programul-romania-start-up-national-2017-pana-la-50-000-de-euro-pentru-deschiderea-unei-afaceri.html","link"],
        ["link"=>"http://www.finantare.ro/","link"]
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagName"=>"div",
                    "tagClass" => "content"
                ]
            ,
            "short-description" =>
                [
                    "default"=>false,
                ],
            "title"=>
            [
                "default"=>false,
                "tagName"=>"h1",
                "tagClass"=>"entry-title"
            ]
            ,
            "content"=>
                [
                    "tagName"=>"div",
                    "tagClass"=>"entry-content",
                    "remove-links"=>true,
                ]
            ,
            "author"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"a",
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'rel', 'tagAttributeValue' => 'author'],
                ]
            ,
            "date"=>
            [
                "tagName"=>"time",
                "tagClass"=>"entry-date updated"
            ]
            ,
            "time"=>
            [
                "tagName"=>"a",
                "tagClass"=>"post-time",
                "attribute_return" => "title",
            ]
        ];
}