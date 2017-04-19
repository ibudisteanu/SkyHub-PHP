<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_HotnewsRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://hotnews.ro/';
    public $sWebsiteName = 'Hotnews';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('hotnews.ro');

    public $arrPages = [
        ["link"=>"http://www.hotnews.ro/stiri-international-21717357-esec-coreei-nord-nou-test-racheta-care-explodat-aproape-imediat-dupa-lansare.htm","link"],
        ["link"=>"http://www.hotnews.ro/","link"]
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagName"=>"div",
                    "tagClass" => "articol_render"
                ]
            ,
            "short-description" =>
                [
                    "default"=>false,
                ],
            "content"=>
                [
                    "tagId"=>"articleContent",
                    "remove-links"=>true,
                ]
            ,
            "author"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"div",
                            "tagClass"=>"autor"
                        ],
                    "tagName"=>"a",
                ]
            ,
            "date"=>
                [
                    "tagName"=>"span",
                    "tagClass" => "data",
                ]
            ,
            "time"=>
                [
                    "tagName"=>"span",
                    "tagClass"=>"data",
                ]
        ];
}