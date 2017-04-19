<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//require APPPATH.'libraries';

class Register_validator extends Validator
{
    public $Users;

    public function __construct($Users)
    {
        parent::__construct();
        $this->Users=$Users;
        $this->sFormName='register';
    }

    /*
    public function CheckPosts2()
    {
        $config = array(
            array(
                'field' => 'fullname',
                'label' => 'Full name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'username',
                'label' => 'User name',
                'rules' => 'trim|is_unique[users.user_nicename]',
            ),
            array(
                'field' => 'email',
                'label' => 'E-mail',
                'rules' => 'trim|required|valid_email|is_unique[users.user_email]',
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required',
            )
        );

        $this->form_validation->set_rules($config);

        if($this->form_validation->run() === false){
    }
    */

    public function CheckPosts()
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['username','<strong>Username</strong>'],
                ['email','<strong>Email</strong>'],
                ['firstName','<strong>First Name</strong>'],
                ['lastName','<strong>LastName</strong>'],
                ['password','<strong>Password</strong>'],
                ['country','<strong>Country</strong>'],
                ['city','<strong>City</strong>'],
                //,['rePassword','<strong>Retry Password</strong>']
            ]);

        if ($sError!='')
        {
            $this->sError =$sError.' Not Specified';
            return false;
        }

        $username = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-username'], 'html|xss|whitespaces');
        $email = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-email'], 'html|xss|whitespaces');
        $firstName = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-firstName'], 'html|xss|whitespaces');
        $lastName = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-lastName'], 'html|xss|whitespaces');
        $password = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-password'], 'html|xss|whitespaces');
        $country = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-country'], 'html|xss|whitespaces');
        $city = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-city'], 'html|xss|whitespaces');
        //$rePassword = $this->StringsAdvanced->processText($_POST['rePassword'], 'html|xss|whitespaces');

        /*if ($password != $rePassword)
        {
            $this->sError = "The passwords introduced don't match";
            return false;
        }*/

        if (! $this->checkValidUsername($username))
            $sError .= "<strong>Username</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidPassword($password))
            $sError .= "<strong>Password</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidName($firstName))
            $sError .= "<strong>FirstName</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidName($lastName))
            $sError .= "<strong>LastName</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidName($city,3))
            $sError .= "<strong>City</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidEmail($email))
            $sError .= "<strong>Email Address</strong> ".$this->sError.'<br/>';

        if ($this->Users->userByUsername($username) != null)
            $sError .= 'Username <strong>' . $username . '</strong> is already used by somebody else'.'<br/>';

        if ($this->Users->userByEmail($email) != null)
            $sError .= $this->sError='Email <strong>'.$email.'</strong> is already used'.'<br/>';

        echo $sError;
        if ($sError != '')
        {
            $this->CI->StringsAdvanced->removeLastBRTag($sError);

            $this->sError=$sError;
            return false;
        }

        return true;

    }

}