<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_FonduriStructuraleRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://www.fonduri-structurale.ro/';
    public $sWebsiteName = 'Fonduri Structurale';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('fonduri-structurale.ro','www.fonduri-structurale.ro');

    public $arrPages = [
        ["link"=>"http://www.fonduri-structurale.ro/stiri/18393/start-up-nation-ghidul-solicitantului-va-fi-publicat-imediat-dupa-paste","link"],
        ["link"=>"http://www.fonduri-strucutrale.ro","link"]
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagName"=>"div",
                    "tagClass" => "container-fluid content"
                ],
            "title"=>
                [
                    "tagName"=>"h1",
                ]
            ,
            "content"=>
                [
                    "tagName"=>"div",
                    "tagClass"=>"dynamic-content",
                    "remove-links"=>true,
                ]
            ,
            "date"=>
                [
                    "tagName"=>"time",
                ]
            ,
        ];
}