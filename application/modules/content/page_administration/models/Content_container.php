<?php

require_once APPPATH.'modules/content/page_administration/models/Content_object.php';

class Content_container extends MY_Model
{
    public $arrContent=[];

    function __construct()
    {
        parent::__construct('');
    }

    function addObject($sData='',$sHeader='',$iID=-1000,$sEnd='')
    {
        if ($iID==-1000)
            $iID = count($this->arrContent);

        /*
        echo  'object: '.$sData.$iID;
        if ($iID == 5) {
            $backtrace = debug_backtrace();
            print_r($backtrace);
        }*/

        if (strlen($sHeader) > 0)
        {
            //echo $sHeader;

            $start = strpos($sHeader, '<');
            $length1 = strpos($sHeader, '>') - $start;
            $length2 = strpos($sHeader, ' ') - $start;

            if (($length2>0)&&($length2 < $length1)) {
                $length = $length2;
            }
            else{
                $length=$length1;
            }
            $src = '</'.substr($sHeader, $start+1, $length-1);
            $src.='>';
            $sEnd=$src;
            //echo $sHeader.'   ... '.$sEnd;
            //echo $src;
        }

        $ContentObject = new Content_object($sHeader,$sData,$iID,$sEnd);
        array_push($this->arrContent,$ContentObject);
    }

    function renderViewByOrder()
    {
        $data['arrContent']=$this->arrContent ;
        $this->load->view('page_administration/content_container_view',$data);
    }

}