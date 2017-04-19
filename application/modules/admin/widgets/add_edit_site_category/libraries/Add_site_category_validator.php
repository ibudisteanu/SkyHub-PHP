<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH.'libraries';

class Add_site_category_validator extends Validator
{
    public $Users;

    public function __construct($Users)
    {
        parent::__construct();

        $this->Users=$Users;
        $this->sFormName='addSiteCategory';
    }

    public function CheckPosts($SiteCategories)
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['name','<strong>Name</strong>'],
                ['parentCategory','<strong>Parent Category</strong>'],
                ['imageIcon','<strong>Image Icon</strong>'],
                ['importance','<strong>Importance Factor</strong>'],
                //['imageUpload','<strong>Image Upload</strong>'],
                ['coverImage','<strong>Cover Image</strong>'],
                //['coverImageUpload','<strong>Cover Image Upload</strong>'],
                ['shortDescription','<strong>Short Description</strong>'],
                ['description','<strong>Description</strong>'],
                ['inputKeywords','<strong>Keywords</strong>']
            ]);

        if ($sError!='')
        {
            $this->sError =$sError.' Not Specified';
            return false;
        }



        $name = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-name'], 'html|xss|whitespaces');
        $parentCategoryId = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-parentCategory'], 'html|xss|whitespaces');
        $iImportanceValue = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-importance'], 'html|xss|whitespaces');
        $imageIcon = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-imageIcon'], 'html|xss|whitespaces');
        $imageUpload = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-imageUpload']['name'], 'html|xss|whitespaces');
        $coverImage = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-coverImage'], 'html|xss|whitespaces');
        $coverImageUpload = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-coverImageUpload']['name'], 'html|xss|whitespaces');

        $sDescription = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-description'], 'xss|whitespaces');
        $sShortDescription = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-shortDescription'], 'xss|whitespaces');
        $sInputKeywords = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-inputKeywords'], 'html|xss|whitespaces');

        if ($parentCategoryId!= '')
            if ($SiteCategories->findCategory($parentCategoryId)==null)
                $sError .= "<strong>Invalid Category Id ".$parentCategoryId." </strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($name))
            $sError .= "<strong>Name</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($sDescription,3))
            $sError .= "<strong>Description</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($sShortDescription,3))
            $sError .= "<strong>Short Description</strong> - ".$this->sError.'<br/>';

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