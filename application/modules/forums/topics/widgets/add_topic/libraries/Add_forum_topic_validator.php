<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH.'libraries';

class Add_forum_topic_validator extends Validator
{
    public $Users;

    public function __construct($Users)
    {
        parent::__construct();

        $this->Users=$Users;
        $this->sFormName='addForumTopic';
    }

    public function CheckPosts()
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['title','<strong>Title</strong>'],
                ['image','<strong>Image Icon</strong>'],
                //['importance','<strong>Importance Factor</strong>'],
                //['urlName','<strong>URL Name'],
                ['coverImage','<strong>Cover Image</strong>'],
                //['shortDescription','<strong>Short Description</strong>'],
                ['bodyCode','<strong>Body Code</strong>'],
                ['inputKeywords','<strong>Keywords</strong>'],
                ['country','<strong>Country</strong>'],
                ['city','<strong>City</strong>']
            ]);

        if ($sError!='')
        {
            $this->sError =$sError.' Not Specified';
            return false;
        }

        $sTitle = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-title'],'xss|whitespaces');

        if (isset($_POST[$this->sFormName.'-importance'])) $iImportanceValue=$this->StringsAdvanced->processText($_POST[$this->sFormName.'-importance'], 'html|xss|whitespaces');
        else $iImportanceValue=0;

        if (isset($_POST[$this->sFormName.'-urlName'])) $sURLName = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-urlName'],'html|xss|whitespaces');
        else $sURLName= '';

        $imageIcon = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-image'] ,'html|xss|whitespaces');

        $imageUpload = '';
        if (isset($_FILES[$this->sFormName.'-imageUpload']))
            $imageUpload = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-imageUpload']['name'],'html|xss|whitespaces');

        $sBodyCode = $this->StringsAdvanced->processText($_POST['addForumTopic-bodyCode'],'xss|whitespaces');

        if (isset($_POST[$this->sFormName.'-shortDescription']))
            $sShortDescription =  $this->StringsAdvanced->processText($_POST[$this->sFormName.'-shortDescription'],'xss|whitespaces');
        else $sShortDescription = '';

        if ($sShortDescription == '') $sShortDescription = $sBodyCode;

        if (strlen($sShortDescription) > 800)
            $sShortDescription = substr($sShortDescription, 0 , 800) . '...';

        $sShortDescription =  $this->StringsAdvanced->closeTags($sShortDescription);

        /*
        $coverImage=stripslashes($_POST[$this->sFormName.'-coverImage']);
        $coverImageUpload = stripslashes($_FILES[$this->sFormName.'-coverImageUpload']['name']);
        */

        if (! $this->checkValidText($sTitle, 5))
            $sError .= "<strong>Name</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText(strip_tags($sBodyCode), 4))
            $sError .= "<strong>Message</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidText($sShortDescription, 4))
            $sError .= "<strong>Short Description</strong> - ".$this->sError.'<br/>';

        if (! $this->checkValidURLName($sURLName,0))
            $sError .= "<strong>URL Name</strong> - ".$this->sError.'<br/>';

        //print_r($this->checkValidKeywords(stripslashes($_POST[$this->sFormName.'-keywords'])));

        if (! $this->checkValidDouble($iImportanceValue))
            $sError .= "<strong>Importance</strong> - ".$this->sError.'<br/>';

        /*

        if (($imageIcon == '' ) && ($imageUpload==''))
            $sError .= "<strong>No Image Upload</strong> - ".$this->sError.'<br/>';

        if (($coverImage == '') && ($coverImageUpload==''))
            $sError .= "<strong>No Cover Image Upload</strong> - ".$this->sError.'<br/>';
        */

        if ($sError != '')
        {
            $this->CI->StringsAdvanced->removeLastBRTag($sError);

            $this->sError=$sError;
            return false;
        }

        return true;
    }

}