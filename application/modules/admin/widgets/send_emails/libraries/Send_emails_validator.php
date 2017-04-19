<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH.'libraries';

class Send_emails_validator extends Validator
{
    public $Users;
    public $Categories;

    public function __construct($Users, $Categories)
    {
        parent::__construct();

        $this->Users=$Users;
        $this->Categories=$Categories;
        $this->sFormName='sendEmails';
    }

    public function CheckPosts($SiteCategories=null)
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['selectedUser','<strong>Selected User</strong>'],
                ['selectedCategory','<strong>Selected Category</strong>'],
                ['actionTemplate','<strong>Action Template</strong>'],
                ['titleSubject','<strong>Title Subject</strong>'],
                ['body','<strong>Body</strong>']
            ]);

        if ($sError!='')
        {
            $this->sError =$sError.' Not Specified';
            return false;
        }
        $sSelectedUser=$this->StringsAdvanced->processText($_POST[$this->sFormName.'-selectedUser'], 'html|xss|whitespaces');
        $sSelectedCategory=$this->StringsAdvanced->processText($_POST[$this->sFormName.'-selectedCategory'], 'html|xss|whitespaces');
        $sActionTemplate=$this->StringsAdvanced->processText($_POST[$this->sFormName.'-actionTemplate'], 'html|xss|whitespaces');
        $sTitleSubject=$this->StringsAdvanced->processText($_POST[$this->sFormName.'-titleSubject'], 'html|xss|whitespaces');
        $sBody = $this->StringsAdvanced->processText($_FILES[$this->sFormName.'-body'], 'xss|whitespaces');

        if ($sSelectedCategory != '')
            if ($this->Categories->exists($sSelectedCategory)==null)
                $sError .= "<strong>Invalid Category ".$sSelectedCategory." </strong> - ".$this->sError.'<br/>';

        if ($sSelectedUser != '')
            if ($this->Users->exists($sSelectedUser)==null)
                $sError .= "<strong>Invalid User ".$sSelectedUser." </strong> - ".$this->sError.'<br/>';

        if (($sSelectedUser != '') && ($sSelectedCategory != ''))
            $sError .= "<strong>No User or Category selected ".$this->sError.'<br/>';

        if ($sActionTemplate != 'custom body')
        {
            if (strlen($sBody) < 4)
            {
                $sError .= "<strong>Length too small - at least 4 chars</strong> - ".$this->sError.'<br/>';
            }

            if (! $this->checkValidText($sTitleSubject))
                $sError .= "<strong>Title/Subject</strong> - ".$this->sError.'<br/>';

        }

        if ($sError != '')
        {
            $this->CI->StringsAdvanced->removeLastBRTag($sError);

            $this->sError=$sError;
            return false;
        }

        return true;
    }

}