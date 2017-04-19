<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH.'libraries';

class Add_forum_category_validator extends Validator
{

    public function __construct()
    {
        parent::__construct();

        $this->sFormName='addForumCategory';

        $this->loadUsersModel();
    }

    public function CheckPosts()
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['name','<strong>Name</strong>'],
                ['imageIcon','<strong>Image Icon</strong>'],
                //['importance','<strong>Importance Factor</strong>'],
                ['coverImage','<strong>Cover Image</strong>'],
                //['urlName','<strong>URL Name'],
                ['description','<strong>Description</strong>'],
                ['inputKeywords','<strong>Keywords</strong>']
            ]);

        if ($sError!='')
        {
            $this->sError =$sError.' Not Specified';
            return false;
        }

        $name = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-name'], 'html|xss|whitespaces');

        if (isset($_POST[$this->sFormName.'-importance'])) $iImportanceValue = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-importance'], 'html|xss|whitespaces');
        else $iImportanceValue=0;

        if (isset($_POST[$this->sFormName.'-urlName'])) $sURLName = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-urlName'], 'html|xss|whitespaces');

        $imageIcon = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-imageIcon'], 'html|xss|whitespaces');
        $imageUpload = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-imageUpload']['name'], 'html|xss|whitespaces');
        $sDescription = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-description'], 'xss|whitespaces');
        $coverImage = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-coverImage'], 'html|xss|whitespaces');
        $coverImageUpload = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-coverImageUpload']['name'], 'html|xss|whitespaces');

        if (! $this->checkValidText($name))
            $sError .= "<strong>Name</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($sDescription))
            $sError .= "<strong>Description</strong> - ".$this->sError.'<br/>';

        if (isset($sURLName))
            if (! $this->checkValidURLName($sURLName,0))
                $sError .= "<strong>URL Name</strong> - ".$this->sError.'<br/>';

        //print_r($this->checkValidKeywords(stripslashes($_POST[$this->sFormName.'-keywords'])));

        if (! $this->checkValidDouble($iImportanceValue))
            $sError .= "<strong>Importance</strong> - ".$this->sError.'<br/>';

        if (($imageIcon == '' ) && ($imageUpload==''))
            $sError .= "<strong>No Image Upload</strong> - ".$this->sError.'<br/>';

        if (($coverImage == '') && ($coverImageUpload==''))
            $sError .= "<strong>No Cover Image Upload</strong> - ".$this->sError.'<br/>';

        if ($sError != '')
        {
            $this->CI->StringsAdvanced->removeLastBRTag($sError);

            $this->sError=$sError;
            return false;
        }

        return true;
    }

}