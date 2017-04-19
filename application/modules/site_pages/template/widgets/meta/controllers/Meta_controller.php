<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Meta_controller extends MY_Controller
{
    public $SchemaMarkup;

    protected $sMetaPageType;
    protected $sMetaURL;
    protected $arrMetaImages;
    protected $sMetaTitle;
    protected $sMetaKeywords;
    protected $sMetaDescription;
    protected $sMetaLanguage = 'en-US';
    protected $sFacebookAPIId= '302184056577324';

    public function __construct()
    {
        parent::__construct(true);

        $this->sMetaPageType = 'article';

        $this->SchemaMarkup = modules::load('schema_markup/schema_markup', NULL);
    }


    public function getRender($sTitle)
    {
        $sData = '';

        if ($this->sMetaTitle=='') $this->sMetaTitle = $sTitle;

        if ($this->sMetaURL == '') $this->sMetaURL = base_url('');
        if ($this->sMetaTitle  == '') $this->sMetaTitle = WEBSITE_NAME.' - Connect, Discover, Talk and Change the World';
        if ($this->arrMetaImages == '') $this->arrMetaImages = base_url('theme/images/SkyHub-cover-image.jpg') ;
        if ($this->sMetaKeywords == '') $this->sMetaKeywords = WEBSITE_META_KEYWORDS;
        if ($this->sMetaDescription  == '')  $this->sMetaDescription = WEBSITE_META_DESCRIPTION;

        $this->data['MetaURL'] = $this->sMetaURL;
        $this->data['MetaImage'] = $this->arrMetaImages;
        $this->data['MetaKeywords'] = $this->sMetaKeywords;
        $this->data['MetaPageType'] = $this->sMetaPageType;
        $this->data['MetaFacebookAPIId'] = $this->sFacebookAPIId;


        $sData .= $this->renderWebsiteMeta();
        $sData .= $this->renderFacebook();
        $sData .= $this->renderTwitter();


        $sData .= $this->SchemaMarkup->renderSchemaMarkups();

        return $sData;
    }

    public function loadMeta($sMetaTitle='', $sMetaDescription ='', $arrMetaImages=null, $sMetaURL = '', $sMetaKeywords='', $sMetaLanguage='', $sMetaPageType='' )
    {
        $sMetaTitle = strip_tags($sMetaTitle);
        $sMetaDescription = strip_tags($sMetaDescription);

        if ($sMetaDescription != '')  $this->sMetaDescription = $sMetaDescription;
        if ($sMetaPageType != '') $this->sMetaPageType = $sMetaPageType;
        if (($sMetaTitle != '')&&($sMetaTitle != WEBSITE_NAME)) $this->sMetaTitle = $sMetaTitle;
        if ($sMetaURL != '') $this->sMetaURL= $sMetaURL;
        if ($arrMetaImages!=null) $this->arrMetaImages = $arrMetaImages;
        if ($sMetaKeywords != '') $this->sMetaKeywords = $sMetaKeywords;
        if ($sMetaLanguage != '') $this->sMetaLanguage = $sMetaLanguage;
    }

    public function getMetaTitle(){
        return $this->sMetaTitle;
    }

    public function getMetaDescription(){
        return $this->sMetaDescription;
    }

    public function getMetaKeywords(){
        return $this->sMetaKeywords;
    }

    protected function renderWebsiteMeta()
    {
        //google
        $sMetaTitle= $this->sMetaTitle;
        if (strlen($sMetaTitle) < 53-strlen(' - '.WEBSITE_TITLE)) $this->addSubStringIfNotPreset($sMetaTitle, WEBSITE_TITLE);
        else
            if (strlen($sMetaTitle) < 53-strlen(' - '.WEBSITE_NAME)) $this->addSubStringIfNotPreset($sMetaTitle, WEBSITE_NAME);
            else $sMetaTitle = substr($sMetaTitle,0,50).'...';

        $sMetaDescription = $this->sMetaDescription;
        if (strlen($sMetaDescription) < 150-strlen(' - '.WEBSITE_TITLE)) $this->addSubStringIfNotPreset($sMetaDescription, WEBSITE_TITLE);
        else if (strlen($sMetaDescription) < 150-strlen(' - '.WEBSITE_NAME)) $this->addSubStringIfNotPreset($sMetaDescription, WEBSITE_NAME);
        else $sMetaDescription = substr($sMetaDescription,0,150);

        $this->data['MetaTitle'] = $sMetaTitle;
        $this->data['MetaDescription'] = $sMetaDescription;
        $this->data['MetaLanguage'] = $this->getLanguage();
        $this->SchemaMarkup->generateWebsiteMarkup($sMetaTitle, $sMetaDescription);

        return $this->renderModuleView('website_meta.php',$this->data, true);
    }

    protected function renderFacebook()
    {
        //facebook
        $sMetaTitle= $this->sMetaTitle;
        if (strlen($sMetaTitle) < 60-strlen(' - '.WEBSITE_TITLE))  $this->addSubStringIfNotPreset($sMetaTitle, WEBSITE_TITLE);
        else  if (strlen($sMetaTitle) < 60-strlen(' - '.WEBSITE_NAME ))  $this->addSubStringIfNotPreset($sMetaTitle, WEBSITE_NAME);
        else $sMetaTitle = substr($sMetaTitle,0,60);

        $sMetaDescription = $this->sMetaDescription;
        if (strlen($sMetaDescription) < 110-strlen(' - '.WEBSITE_TITLE)) $this->addSubStringIfNotPreset($sMetaDescription, WEBSITE_TITLE);
        else if (strlen($sMetaDescription) < 110-strlen(' - '.WEBSITE_NAME)) $this->addSubStringIfNotPreset($sMetaDescription, WEBSITE_NAME);
        else $sMetaDescription = substr($sMetaDescription,0,110);

        $this->data['MetaTitle'] = $sMetaTitle;
        $this->data['MetaDescription'] = $sMetaDescription;
        return $this->renderModuleView('facebook_meta_view',$this->data, true);
    }

    protected function renderTwitter()
    {
        //twitter - limit 116 description (92 with images on) and 70 Title

        $sMetaTitle= $this->sMetaTitle;
        if (strlen($sMetaTitle) < 50-strlen(' - '.WEBSITE_TITLE))  $this->addSubStringIfNotPreset($sMetaTitle, WEBSITE_TITLE);
        else if (strlen($sMetaTitle) < 50-strlen(' - '.WEBSITE_NAME))  $this->addSubStringIfNotPreset($sMetaTitle, WEBSITE_NAME);
        else $sMetaTitle = substr($sMetaTitle,0,70);

        $sMetaDescription = $this->sMetaDescription;
        if (strlen($sMetaDescription) < 110-strlen(' - '.WEBSITE_TITLE)) $this->addSubStringIfNotPreset($sMetaDescription, WEBSITE_TITLE);
        else if (strlen($sMetaDescription) < 110-strlen(' - '.WEBSITE_NAME)) $this->addSubStringIfNotPreset($sMetaDescription, WEBSITE_NAME);
        else $sMetaDescription = substr($sMetaDescription,0,110);

        $this->data['MetaTitle'] = $sMetaTitle;
        $this->data['MetaDescription'] = $sMetaDescription;

        return $this->renderModuleView('twitter_meta_view',$this->data, true);
    }


    private function addSubStringIfNotPreset(&$str, $addStr,$sLink=' - ')
    {
        if (strpos($str,$addStr) === FALSE)
            $str .= $sLink.$addStr;
    }

    public function getLanguage()
    {
        return $this->sMetaLanguage;
    }

}