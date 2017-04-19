<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_Antena3Ro_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://antena3.ro/';
    public $sWebsiteName = 'Antena3';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('antena3.ro');

    public $arrPages = [
        ["link"=>"http://www.antena3.ro/politica/gabriel-oprea-justitie-dosar-senat-378324.html","link"],
        ["link"=>"http://antena3.ro/","link"],
        ["link"=>"http://www.antena3.ro/actualitate/se-uneste-dreapta-nicusor-dan-vom-face-un-guvern-cu-pnl-dar-nu-si-alianta-376915.html","Stiri"],
        ["link"=>"http://www.antena3.ro/actualitate/baltag-sinteza-gadea-magistrati-judecatori-376586.html","Stiri"],
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagName"=>"div",
                    "tagClass" => "c1"
                ]
            ,
            "content"=>
                [
                    "tagName"=>"div",
                    "tagClass"=>"text",
                    "remove-links"=>true,
                ]
            ,
            "author"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"span",
                            "tagClass"=>"fl"
                        ],
                    "tagName"=>"a",
                    "attribute_return"=>"link",
                ]
            ,
            "date"=>
                [
                    "tagName"=>"span",
                    "tagClass" => "fl",
                ]
            ,
            "time"=>
                [
                    "tagName"=>"span",
                    "tagClass" => "fl",
                ]
            ,
            "image"=>
                [
                    "image-preview-alt"=>
                        [
                            "tagId"=>"ivm-preroll-image",
                            "attribute_return"=>"alt",
                        ]
                ]
            ,
        ];
}