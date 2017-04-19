<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_StiriPeSurseRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://stiripesurse.ro/';
    public $sWebsiteName = 'Stiri pe Surse';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('stiripesurse.ro');

    public $arrPages = [
        ["link"=>"http://www.stiripesurse.ro/un-nou-cutremur-in-buzau-ce-magnitudine-a-avut_1151990.html","stiri"],
        ["link"=>"http://www.stiripesurse.ro/stirile-zilei","Stiri"],
        ["link"=>"http://www.stiripesurse.ro/politica","Politica"],
        ["link"=>"http://www.stiripesurse.ro/interviuri","Interviuri"],
        ["link"=>"http://www.stiripesurse.ro/externe","Externe"],
        ["link"=>"http://www.stiripesurse.ro/sanatate","Sanatate"],
        ["link"=>"http://www.stiripesurse.ro/economie","Economie"],
        ["link"=>"http://www.stiripesurse.ro/social","Social"],
        ["link"=>"http://www.stiripesurse.ro/cultura-si-media","Cultura si Media"],
        ["link"=>"http://www.stiripesurse.ro/uniunea-europeana","Uniunea Europeana"],
        ["link"=>"http://www.stiripesurse.ro/sinteze","Sinteze"],
        ["link"=>"http://www.stiripesurse.ro/arhiva/","Arhiva"],
        ["link"=>"http://www.stiripesurse.ro/sport","sport"],
        ["link"=>"http://www.stiripesurse.ro/politicscan","Politic Scan"],
        ["link"=>"http://www.stiripesurse.ro/","General"],
    ];

    public $arrUnFollowPages = [];

    public $arrCrawlerExtraction =
    [
        "container" =>
            [
                "tagName"=>"article",
                "tagClass" => "post article-single"
            ]
        ,
        "title"=>
            [
                "tagName"=>"h1",
                "tagClass"=>"article-single-title"
            ]
        ,
        "content"=>
            [
                "tagName"=>"section",
                "tagClass"=>"article-content",
                "remove-links"=>true,
            ]
        ,
        "tags"=>
            [
                "find-element-first"=>
                    [
                        "tagName"=>"a",
                    ],
                'tagAttribute' => ['tagAttributeName' => 'rel', 'tagAttributeValue' => 'tag' ],
            ]
        ,
        "author"=>
            [
                "find-element-first"=>
                    [
                        "tagName"=>"div",
                        "tagClass"=>"article-single-meta"
                    ],
                "tagName"=>"a",
                "attribute_return"=>"link",
            ]
        ,
        "time"=>
            [
                "tagName"=>"time",
                "tagClass" => "op-published",
                "attribute_return"=>"datetime",
            ]
        ,
    ];

}