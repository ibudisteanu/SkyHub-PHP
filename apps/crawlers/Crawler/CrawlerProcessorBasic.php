<?php


class CrawlerProcessorBasic
{
    public $CrawlerStrings;
    public $CrawlerVisitedSites;
    public $CrawlerTags;
    public $User;
    public $iPagesCrawled=0;

    public function __construct()
    {
        set_time_limit(0);

        $this->CrawlerStrings = new CrawlerStrings();
        $this->CrawlerVisitedSites = new CrawlerVisitedSites();
        $this->CrawlerTags = new CrawlerTags();
        $this->User = new User();

        $this->test();
    }

    protected function checkElement($DOM, $DOMElementList, $PageElement)
    {
        if ($PageElement == null) return null;

        $result = array();

        if (isset($PageElement['find-element-first']))
        {
            $array = array();
            foreach ($DOMElementList as $DOMElement)
            {
                $DOMElementNew = $this->checkElement($DOM, array($DOMElement), $PageElement['find-element-first']);
                $array = array_merge($array, $DOMElementNew);
            }

            $DOMElementList = $array;
        }

        foreach ($DOMElementList as $DOMElement)
            if ($DOMElement != null)
            {

                if (isset($PageElement['tagId']))
                {
                    array_push($result,$DOM->getElementById($PageElement['tagId']));
                } else
                    if (isset($PageElement['tagClass']))
                    {
                        $result=array_merge($result,$this->getElementsByClass($DOMElement,(isset($PageElement['tagName']) ? $PageElement['tagName'] : ''), $PageElement['tagClass']));
                    } else
                        if (isset($PageElement['tagName']))
                        {
                            $rez = $DOMElement->getElementsByTagName($PageElement['tagName']);

                            foreach ($rez as $item)
                                array_push($result, $item);
                        }
                        else
                            if (isset($PageElement['tagAttribute']))
                            {
                                $rez = $this->getElementsByAttribute($DOMElement,$PageElement['tagAttribute']['tagAttributeName'],$PageElement['tagAttribute']['tagAttributeValue']);

                                foreach ($rez as $item)
                                    array_push($result, $item);
                            }
            }

        for ($index=0; $index < count($result); $index++) {
            $rez = $result[$index];
            if ($rez != null) {
                if (isset($PageElement['attribute_return'])) {
                    if ($PageElement['attribute_return'] == 'link')
                    {
                        if ($rez->getAttribute('href') != null) {
                            $sHref = $rez->getAttribute('href');
                            $sText = $rez->nodeValue;
                            $this->CrawlerStrings->repairURL($sHref);
                            $result[$index] = '<a href="'.$sHref.'">'.$sText.'</a>';
                        }
                    } else
                        if ($PageElement['attribute_return'] == 'explode')
                        {
                            foreach ($PageElement['explode'] as $explode)
                            {
                                $sValue = $rez->nodeValue;
                                $arrData = explode($explode['chars'], $sValue);
                                if ((count($arrData) > $explode['split_return']) && (count($arrData)==$explode['count']))
                                {
                                    $result[$index] = $arrData[$explode['split_return']];
                                    if (isset($explode['remove chars']))
                                        $result[$index] = substr($result[$index], $explode['remove chars']);
                                }
                            }
                        }
                        else
                            if ($rez->getAttribute($PageElement['attribute_return']) != null) {
                                $result[$index] = $rez->getAttribute($PageElement['attribute_return']);
                            }
                }
            }
        }

        return $result;
    }

    protected function getElementsByClass(&$parentNode, $tagName, $className) {
        $nodes=array();

        if ($tagName != '')
            $childNodeList = $parentNode->getElementsByTagName($tagName);
        else
            $childNodeList = $parentNode;

        if (isset($childNodeList->length))
        {
            for ($i = 0; $i < $childNodeList->length; $i++) {
                $temp = $childNodeList->item($i);
                if (stripos($temp->getAttribute('class'), $className) !== false)
                    $nodes[] = $temp;
            }
        } else
        {
            if (stripos($parentNode->getAttribute('class'), $className) !== false)
                $nodes [] = $parentNode;
        }

        return $nodes;
    }

    protected function getElementsByAttribute(&$parentNode, $tagAttributeName, $tagAttributeValue) {
        $nodes=array();

        if (isset($parentNode->length))
        {
            for ($i = 0; $i < $parentNode->length; $i++) {
                $temp = $parentNode->item($i);
                if (stripos($temp->getAttribute($tagAttributeName), $tagAttributeValue) !== false)
                    $nodes[] = $temp;
            }
        } else
        {
            if (stripos($parentNode->getAttribute($tagAttributeName), $tagAttributeValue) !== false)
                $nodes [] = $parentNode;
        }

        return $nodes;
    }

    protected  function extractValueFromDOMElement( $elementList, $bArray=false, $bReturnOnlyText=false )
    {
        if ($elementList == null) return '';

        $result = array();

        foreach ($elementList as $element)
        {
            $sTextElement = '';
            if (is_object($element)) $sTextElement  = (string) $element->nodeValue ;
            else
                if (is_string($element)) $sTextElement = $element;
                else $sTextElement = (string) $element;

            if (!in_array($sTextElement, $result))
                array_push($result, $sTextElement);
        }

        if ($bArray) {
            if (!is_array($result)) $result = array($result, false);
        } else
            if (!$bArray) {

                if (is_array($result))
                    if (count($result) > 0)
                        $result = $result[0];
                    else $result = null;
            }

        if ($bReturnOnlyText)
            $result = strip_tags($result);

        return $result;
    }







    private function test()
    {
        if ($this->User->login("muflonel2000"))  echo 'sunt logat<br/>';
        else echo 'nu sunt logat <br/>';

        if ($this->User->logout())  echo 'm-am delogat <br/>';
        else echo 'nu m-am delogat <br/>';

        if ($this->CrawlerVisitedSites->checkUploadedSite('www.acasa.ro','') == -1)
            $this->CrawlerVisitedSites->addUploadedSite('www.acasa.ro','');
        else
            echo 'i found it<br/>';

        if ($this->CrawlerVisitedSites->checkUploadedSite('www.nodsoft.ro','') == -1)
            $this->CrawlerVisitedSites->addUploadedSite('www.nodsoft.ro','');
        else
            echo 'i found it 2<br/>';

        //$this->CrawlerVisitedSites->addUploadedSite('www.nodsoft.ro222','');
    }

}