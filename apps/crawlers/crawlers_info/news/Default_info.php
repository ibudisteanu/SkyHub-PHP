<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_Default_info extends CrawlerDataInfo
{
    public $arrCrawlerExtraction =
        [
            "title"=>
                [
                    'tagName'=>'title'
                ]
            ,
            "title-og"=>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'property', 'tagAttributeValue' => 'og:title' ],
                    "attribute_return" => "content",
                ]
            ,
            "meta-short-description" =>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'name', 'tagAttributeValue' => 'description'],
                    "attribute_return" => "content",
                ],
            "short-description-og" =>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'property', 'tagAttributeValue' => 'og:description' ],
                    "attribute_return" => "content",
                ]
            ,
            "meta-keywords"=>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'name', 'tagAttributeValue' => 'keywords' ],
                    "attribute_return" => "content",
                ]
            ,
            "meta-author"=>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'name', 'tagAttributeValue' => 'author' ],
                    "attribute_return" => "content",
                ]
            ,
            "html-language"=>
                [
                    'tagName'=>'html',
                    "attribute_return" => "ro",
                ]
            ,
            "meta-language"=> //<meta name="language" content="Romanian" />  NOT RECOMMENDED because language must be ISO2 [2 chars only]
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'name', 'tagAttributeValue' => 'language' ],
                    "attribute_return" => "content",
                ]
            ,
            "meta-language2"=> //<meta http-equiv="content-language" content="ro" />
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'http-equiv', 'tagAttributeValue' => 'content-language' ],
                    "attribute_return" => "content",
                ]
            ,
            "language-og"=>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'property', 'tagAttributeValue' => 'og:locale' ],
                    "attribute_return" => "content",
                ]
            ,
            "image-og"=>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'property', 'tagAttributeValue' => 'og:image' ],
                    "attribute_return" => "content",
                ]
            ,
            "image-first-container"=>
                [
                    "image-preview"=>
                        [
                            "tagName"=>"img",
                            "attribute_return"=>"src",
                        ],
                    "image-preview-alt"=>
                        [
                            "tagName"=>"img",
                            "attribute_return"=>"alt",
                        ]
                ]
            ,
            "tags-og"=>
                [
                    "find-element-first"=>
                        [
                            'tagName'=>'meta',
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'property', 'tagAttributeValue' => 'article:tag' ],
                    "attribute_return" => "content",
                ]

        ];
}