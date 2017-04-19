<?php

class URI_Processing_Library
{
    public $arrFormParam;
    public $sFormAction;
    public $sFormId;

    //public $sAnchorElementId;

    public $iPageIndex=0;
    public $sFormFullURL;

    public $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function processRoutingURISegments($arrSegments, $iStartingURIIndex)
    {
        try
        {

            $iCount = 0;
            while ($iCount < $iStartingURIIndex)
            {
                unset($arrSegments[$iCount]);
                $iCount++;
            }

            $this->arrFormParam = [];
            foreach ($arrSegments as $segment)
                array_push($this->arrFormParam, $segment);



            $this->calculateParameters();

        }
        catch (Exception $ex)
        {

        }

        return $arrSegments;
    }

    protected function calculateParameters()
    {
        //check if last element is Anchor Element Id for Scroll
        /*if (count($this->arrFormParam) > 0) {

            $string = $this->arrFormParam[count($this->arrFormParam) - 1];
            echo $string;
            if ((strlen($string) > 0)&&($string[0] == '#'))
                $this->sAnchorElementId = array_pop($this->arrFormParam);
        }*/

        try {
            for ($index = 0; $index < count($this->arrFormParam); $index++)
                if ((strtolower($this->arrFormParam[$index]) == 'page') || (strtolower($this->arrFormParam[$index]) == 'pages')) {

                    if (count($this->arrFormParam) > $index+1) {

                        if (ctype_xdigit($this->arrFormParam[$index + 1]))
                            $this->iPageIndex = (int)$this->arrFormParam[$index + 1];

                        array_splice($this->arrFormParam, $index, 2);

                        break;
                    }
                }
        } catch (Exception $ex)
        {
            $this->parent->showErrorPage('Error getting the page index','error','Forum Category','Forum Category');
        }

        $this->parent->checkValidAction();

        //Check if is MongoID
        if (count($this->arrFormParam) > 0)
        {
            $string = $this->arrFormParam[count($this->arrFormParam) - 1];
            if ((ctype_xdigit($string)) && (MongoId::isValid($string)))
                $this->sFormId = array_pop($this->arrFormParam);
        }

        $sFullURL = '';
        foreach ($this->arrFormParam as $param)
            $sFullURL .= $param."/";

        $sFullURL = urldecode($sFullURL);

        $this->sFormFullURL = rtrim($sFullURL ,'/');
    }


}