<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH.'libraries';

class Add_forum_validator extends Validator
{
    public $Users;

    public function __construct($Users)
    {
        parent::__construct();

        $this->Users=$Users;
        $this->sFormName='addForum';
    }

    public function CheckPosts($SiteCategories)
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['name','<strong>Name</strong>'],
                ['parentCategory','<strong>Parent Category</strong>'],
                ['imageIcon','<strong>Image Icon</strong>'],
                //['importance','<strong>Importance Factor</strong>'],
                //['imageUpload','<strong>Image Upload</strong>'],
                ['coverImage','<strong>Cover Image</strong>'],
                //['coverImageUpload','<strong>Cover Image Upload</strong>'],
                ['description','<strong>Description</strong>'],
                ['detailedDescription','<strong>Detailed Description</strong>'],
                ['inputKeywords','<strong>Keywords</strong>'],
                ['country','<strong>Country</strong>'],
                ['city','<strong>City</strong>']
            ]);

        if ($sError!='')
        {
            $this->sError =$sError.' Not Specified';
            return false;
        }

        $name = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-name'],'html|xss|whitespaces');
        $parentCategory = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-parentCategory'],'html|xss|whitespaces');

        if (isset($_POST[$this->sFormName.'-importance'])) $iImportanceValue = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-importance'],'html|xss|whitespaces');
        else $iImportanceValue=0;

        if (isset($_POST[$this->sFormName.'-urlName']))
            $sURLName = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-urlName'],'html|xss|whitespaces');

        $imageIcon = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-imageIcon'],'html|xss|whitespaces');
        $imageUpload = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-imageUpload']['name'],'html|xss|whitespaces');
        $coverImage = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-coverImage'],'html|xss|whitespaces');
        $coverImageUpload = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-coverImageUpload']['name'],'html|xss|whitespaces');

        $description = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-description'],'xss|whitespaces');
        $sDetailedDescription = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-detailedDescription'],'xss|whitespaces');

        $sInputKeywords = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-inputKeywords'], 'html|xss|whitespaces');

        if ($parentCategory != '')
            if ($SiteCategories->findCategory($parentCategory)==null)
                $sError .= "<strong>Invalid Parent Category ".$parentCategory." </strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($name,3))
            $sError .= "<strong>Name</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($description))
            $sError .= "<strong>Description</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($sDetailedDescription,4))
            $sError .= "<strong>Detailed Description</strong> - ".$this->sError.'<br/>';

        if (isset($sURLName))
            if (! $this->checkValidURLName($sURLName,0))
                $sError .= "<strong>URL Name</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidDouble($iImportanceValue))
            $sError .= "<strong>Importance</strong> - ".$this->sError.'<br/>';

        if (($imageIcon == '' ) && ($imageUpload==''))
            $sError .= "<strong>No Image Upload</strong> - ".$this->sError.'<br/>';

        if (($coverImage == '') && ($coverImageUpload==''))
            $sError .= "<strong>No Cover Image Upload</strong> - ".$this->sError.'<br/>';

        if ($this->checkValidKeywords($sInputKeywords,3) == [])
        $sError .= "<strong>Too few keywords </strong> - ".$this->sError.'<br/>';

        if ($sError != '')
        {
            $this->CI->StringsAdvanced->removeLastBRTag($sError);

            $this->sError=$sError;
            return false;
        }

        return true;
    }

}