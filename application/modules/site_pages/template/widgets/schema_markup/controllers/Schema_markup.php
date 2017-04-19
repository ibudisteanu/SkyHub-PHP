<?php
/**
 * Created by PhpStorm.
 * User: BIT TECHNOLOGIES
 * Date: 10/13/2016
 * Time: 7:04 PM
 * TUTORIAL http://jsonld.com/article/
 */
class Schema_markup extends  MX_Controller
{
    protected  $arrSchemas = [];

    public function generateWebsiteMarkup()
    {
        $data=[
            "@context" => "http://schema.org",
            "@type" => "WebSite",
            "url" => base_url(""),
            "name" => WEBSITE_TITLE,
            "author" => [
                "@type" => "Person",
                "name" => "Ionut Alexandru Budisteanu"
            ],
            "description" => WEBSITE_META_DESCRIPTION,
            "publisher" => "BIT TECHNOLOGIES RO",
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => base_url("s/{search_term}"),
                "query-input" => "required name=search_term"
            ]
        ];


        $sContent = '<script type="application/ld+json">'.json_encode($data,true).'</script>';
        array_push($this->arrSchemas,$sContent);
    }

    public function generateArticleMarkup($sTitle, $sArticle, $sImage, $sDescription, $sKeywords='', $sAuthor='', $sURL='', $sDateCreated='', $sDateModified='', $sEditor='', $sPublisher='', $sDatePublished='' )
    {
        $sTitle = strip_tags($sTitle);
        $sArticle = strip_tags($sArticle);
        $sDescription = strip_tags($sDescription);
        $sKeywords = strip_tags($sKeywords);


        if ($sTitle == '') $sTitle = WEBSITE_TITLE;

        if (strlen($sTitle ) < 53-strlen(' - '.WEBSITE_TITLE)) $this->addSubStringIfNotPreset($sTitle, WEBSITE_TITLE);
        else
            if (strlen($sTitle ) < 53-strlen(' - '.WEBSITE_NAME)) $this->addSubStringIfNotPreset($sTitle, WEBSITE_NAME);
            else $sTitle = substr($sTitle ,0,50).'...';

        if ($sImage == '') base_url('theme/images/SkyHub-cover-image.jpg');
        if ($sDescription == '') $sDescription = WEBSITE_META_DESCRIPTION;

        if (strlen($sDescription) < 160-strlen(' - '.WEBSITE_TITLE)) $this->addSubStringIfNotPreset($sDescription, WEBSITE_TITLE);
        else if (strlen($sDescription) < 160-strlen(' - '.WEBSITE_NAME)) $this->addSubStringIfNotPreset($sDescription, WEBSITE_NAME);
        else $sDescription = substr($sDescription,0,160);

        if ($sKeywords == '') $sKeywords = WEBSITE_META_KEYWORDS;
        if ($sAuthor == '')  $sAuthor = WEBSITE_NAME;
        if ($sURL == '') $sURL = base_url('');

        if ($sEditor =='') $sEditor = $sAuthor;
        if ($sDateModified == '') $sDateModified = $sDateCreated;
        if ($sPublisher == '') $sPublisher = $sAuthor;
        if ($sDatePublished == '') $sDatePublished = $sDateCreated;

        $data = [
            "@context" => "http://schema.org",
            "@type" => "Article",
            "mainEntityOfPage" => [
                "@type"=> "WebPage",
                "@id"=> $sURL,
            ],
            "headline" => $sTitle,
            "image" => [
                "@type" => "ImageObject",
                "url" => $sImage,
                "height" => 600,
                "width" => 700
            ],
            "alternativeHeadline" => substr($sDescription, 140),
            "author" => $sAuthor,
            "editor" => $sEditor,
            "genre" => "search engine optimization",
            "keywords" => $sKeywords,
            "wordcount" => str_word_count($sDescription),
            "publisher" => [
                "@type" => "Organization",
                "name" => $sPublisher,
                "logo" => [
                    "@type" => "ImageObject",
                    "url"=> base_url("theme/images/SkyHub-logo.png")
                ]
            ],
            "url" => $sURL,
            "datePublished" => $sDatePublished,
            "dateCreated" => $sDateCreated,
            "dateModified" => $sDateModified,
            "description" => $sDescription,
            "articleBody" => $sArticle
        ];
        $sContent = '<script type="application/ld+json">'.json_encode($data,true).'</script>';
        array_push($this->arrSchemas,$sContent);
    }

    public function renderSchemaMarkups()
    {
        $sContent = '';
        foreach ($this->arrSchemas as $schema)
            $sContent .= $schema;
        return $sContent;
    }

    private function addSubStringIfNotPreset(&$str, $addStr,$sLink=' - ')
    {
        if (strpos($str,$addStr) === FALSE)
            $str .= $sLink.$addStr;
    }

}