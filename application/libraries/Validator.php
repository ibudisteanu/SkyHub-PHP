<?php

class Validator
{
    public $sError;
    public $sFormName;
    protected $CI;

    function __construct()
    {
        // Assign by reference with "&" so we don't create a copy
        $this->CI = &get_instance();

        $this->CI->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->StringsAdvanced = $this->CI->StringsAdvanced;
    }

    public function loadUsersModel()
    {
        $this->CI->load->model('../modules/users/my_user/users/models/users_minimal', 'Users');
    }

    public function checkFormSets($arrFormInput)
    {
        $sError = '';
        foreach ($arrFormInput as $formInput)
        {
            $formName = $formInput[0];
            $formText = $formInput[1];

            if (!isset($_POST[$this->sFormName.'-'.$formName]))
                $sError .=  "<strong>$formText</strong> ";
        }

        return $sError;
    }

    public function checkValidText($sText, $iMinLength=2, $sFiltration='xss|whitespaces')
    {
        $sText = $this->CI->StringsAdvanced->processText($sText, $sFiltration);

        if (strlen($sText) < $iMinLength)
        {
            $this->sError = "To few letters, min ".$iMinLength;
            return false;
        }

        // each array entry is an special char allowed besides the ones from ctype_alnum

        if ($this->checkIllegalCharacters($sText,''))
        {
            $this->sError = "Invalid Text ". $this->sError;
            return false;
        } else return true;
    }

    public function checkValidKeywords($sText, $iMinLength=2)//return the valid keywords
    {

        //$words = preg_split( "/ (,|;) /", $sText );
        //$words = preg_split("/[\s,;]+/",$sText);
        $words = preg_split("/[,;]+/",$sText);

        if (count($words) < $iMinLength) {
            $this->sError = "Too few keywords. It is necessary to have at least <strong> ".$iMinLength. ' keywords </strong>';
            return [];
        }

        $result = [];
        foreach ($words as $sWord)
        {
            $sWord = preg_replace('/\s+/', ' ', $sWord);
            $sWord = trim($sWord);
            //if ($sWord == ';'); else
            if (!preg_match("/^[a-zA-Z0-9 -]*$/",$sWord)) {
                $this->sError = "Only letters and white space allowed";
                return [];
            } else
            array_push($result,$sWord);
        }

        //var_dump($result);
        return $result;
    }

    public function checkValidDouble($sText)
    {
        if (!(is_numeric($sText) || (is_double($sText) || (is_float($sText) || (is_real($sText))))))
        {
            $this->sError = $sText." is not float number";
            return false;
        }
        return true;
    }

    public function checkValidNumeric($sText)
    {
        if(is_numeric($sText) !== false) {
            $this->sError = "Is not numeric ";
            return false;
        }
        return true;
    }

    public function checkValidLength($sText, $iMinLength=2)
    {

        if (strlen($sText) < $iMinLength)
        {
            $this->sError = "To few letters, min ".$iMinLength;
            return false;
        }

        return true;
    }

    public function checkValidEmail($sEmail)
    {
        if (!filter_var($sEmail, FILTER_VALIDATE_EMAIL))
        {
            $this->sError = "Invalid email format";
            return false;
        }
        return true;
    }

    public function checkValidUsername($sUsername, $iMinLength=3)
    {
        $sUsername = strtolower($this->CI->StringsAdvanced->processText($sUsername,'html|xss|whitespaces'));

        if (strlen($sUsername) < $iMinLength)
        {
            $this->sError = "To few letters in the username, min ".$iMinLength;
            return false;
        }

        // each array entry is an special char allowed besides the ones from ctype_alnum
        $allowed = array(".", "-", "_","$");

        if (($sUsername=='')||( ctype_alnum( str_replace($allowed, '', $sUsername ) ) ))
            return true;
        else
        {
            $this->sError = "Invalid Characters in the input for username";
            return false;
        }
    }

    public function checkValidURLName(&$sURLName, $iMinLength = 4)
    {
        $sURLName = $this->CI->StringsAdvanced->processURLString($sURLName);

        if (strlen($sURLName) < $iMinLength)
        {
            $this->sError = "To few letters in the URL Name, min ".$iMinLength;
            return false;
        }

        return true;
    }

    public function checkValidURL(&$sURL)
    {
        if (!$this->CI->StringsAdvanced->startsWith($sURL,'http://'))
            $sURL = 'http://'.$sURL ;

        if (filter_var($sURL, FILTER_VALIDATE_URL) === FALSE)
        {
            $this->sError = "Invalid URL";
            return false;
        }
        return true;
    }

    function checkValidPassword($sPassword, $iMinLength=5)
    {
        if (strlen($sPassword) <= $iMinLength)
        {
            $this->sError = "To few letters in the password, min ".$iMinLength;
            return false;
        }

        return true;
    }

    function checkValidTimeZone($sTimeZone, $iMinLength=10)
    {
        if (strlen($sTimeZone) <= $iMinLength)
        {
            $this->sError = "To few letters in the TimeZone, min ".$iMinLength;
            return false;
        }

        // each array entry is an special char allowed besides the ones from ctype_alnum
        $allowed = array(".", "-", "_","$",":",",","(",")","/");


        if ( ctype_alnum( str_replace($allowed, '', $sTimeZone ) ) )
            return true;
        else
        {
            $this->sError = "Invalid TimeZone";
            return false;
        }
    }

    protected function checkIllegalCharacters($sString, $sIllegalChars = '[]{}\\|')
    {
        $sFoundChars='';
        for ($index=0; $index < strlen($sString); $index++)
        {
            $char = $sString[$index];

            for ($iCharIndex=0; $iCharIndex < strlen($sIllegalChars); $iCharIndex++)
            {
                if ($char == $sIllegalChars[$iCharIndex])
                {
                    $sFoundChars .= $char.' ';
                    break;
                }
            }
        }

        if ($sFoundChars != '')
        {
            $this->sError .= 'Invalid characters found in your text: '. $sFoundChars;
            return true;
        } else return false;
    }

    function checkValidName($sText, $iMinLength=2)
    {
        if (strlen($sText) <= $iMinLength)
        {
            $this->sError = "To few letters in the Name, min ".$iMinLength;
            return false;
        }

        if ($this->checkIllegalCharacters($sText,'!@#$%^&*()[]{}|\\/<>;~+=|'))
        {
            $this->sError = "Invalid Name ". $this->sError;
            return false;
        } else return true;

        // each array entry is an special char allowed besides the ones from ctype_alnum
        /*
        $allowed = array(".",',', "-", "_","'","",' ','&');

        if ( ctype_alnum( str_replace($allowed, '', $sText ) ) )
            return true;
        else
        {
            $this->sError = "Invalid Name";
            return false;
        }*/
    }

}