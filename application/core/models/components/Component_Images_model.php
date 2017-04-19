<?php

/**
 * COMPONENT that STORE IMAGES and COVERS for TOPICS, FORUMS, CATEGORIES
 */

class Component_Images_model extends CI_Model
{

    public $arrImages=[]; //includes  src, alt, title, type (icon, image, uploaded)
    public $arrCovers=[];

    public function __construct($parent)
    {
        parent::__construct();

        $this->load->library('StringImagesProcessing',null,'StringImagesProcessing');
    }

    public function getImagesArray($bIncludeCovers=false, $bIncludeIcons=false)
    {
        $arrResult = [];
        foreach ($this->arrImages as $image)
            if (($image['src'] != '') && (($bIncludeIcons)||((!$bIncludeIcons)&&(!$this->isIcon($image['src'])))) )
                array_push($arrResult, $image);

        if ($bIncludeCovers)
        {
            foreach ($this->arrCovers as $image)
                if ($image['src'] != '')
                    array_push($arrResult, $image);
        }

        return $arrResult;
    }

    public function getImageFirst($bIncludeIcons=false)
    {
        foreach ($this->arrImages as $image)
            if (($image['src'] != '') && ($image['type'] =='upload') )
                return $image;

        foreach ($this->arrImages as $image)
            if (($image['src'] != '') && (($bIncludeIcons)||((!$bIncludeIcons)&&($image['type']=='icon'))))
                return $image;

        foreach ($this->arrCovers as $image)
            if ($image['src'] != '')
                return $image;

        return null;
    }

    public function getCoverFirst()
    {
        foreach ($this->arrCovers as $imgCover)
            if ($imgCover['src'] != '')
                return $imgCover;
        return null;
    }

    public function getImagesFromBodyCode($sBodyCode)
    {
        if ($sBodyCode != '')
        {
            $imgArray = $this->StringImagesProcessing->getAllImagesFromHTML($sBodyCode);
            $this->removeImagesByType('html');

            foreach ($imgArray as $imageArray)
            {
                $element = [];
                if (isset($imageArray['alt'])) $element = array_merge($element, ['alt'=>$imageArray['alt']]);
                if (isset($imageArray['title'])) $element = array_merge($element, ['title'=>$imageArray['title']]);
                if (isset($imageArray['description'])) $element = array_merge($element, ['description'=>$imageArray['description']]);

                if (isset($imageArray['src']))
                {
                    $element = array_merge($element, ['src'=>$imageArray['src'],'type'=>'html']);

                    array_push($this->arrImages, $element);
                }
            }
        }
    }

    public function getIcon()
    {
        foreach ($this->arrImages as $image)
            if ($image['type'] == 'icon')
                return $image['src'];
    }

    public function setIcon($sValue='')
    {
        foreach ($this->arrImages as $image)
            if ($image['type'] == 'icon') {
                $image['src'] = $sValue;
                return $image;
            }

        $newImage = ['type'=>'icon','src'=>$sValue];
        array_push($this->arrImages,$newImage);
        return$newImage;
    }

    public function loadOldImage($p)
    {
        if (isset($p['Image']))
            $this->insertUploadedImage($p['Image'],(isset($p['ImageAlt'] ) ? $p['ImageAlt'] : ''),(isset($p['ImageTitle']) ? $p['ImageTitle'] : ''));
    }
    public function loadOldCover($p)
    {
        if (isset($p['CoverImage'])) $this->insertUploadedCover($p['CoverImage']);
        else if (isset($p['Cover'])) $this->insertUploadedCover($p['Cover']);
    }

    public function readCursor($p, $bEnableChildren=null)
    {
        if (isset($p['Images'])) $this->arrImages = $p['Images'];
        if (isset($p['Covers'])) $this->arrCovers = $p['Covers'];
    }

    public function serializeProperties()
    {
        $arrResult = [];

        if ((isset($this->arrImages))&&(count($this->arrImages) > 0)) $arrResult = array_merge($arrResult, array("Images"=>$this->arrImages));
        if ((isset($this->arrCovers))&&(count($this->arrCovers) > 0)) $arrResult = array_merge($arrResult, array("Covers"=>$this->arrCovers));

        return $arrResult;
    }

    public function isIcon(&$sIcon)
    {
        if ($this->StringsAdvanced->startsWith($sIcon,"fa")) {
            if (!$this->StringsAdvanced->startsWith($sIcon,"fa fa")) $sIcon = "fa ". $sIcon;
            return true;
        } else
            if ($this->StringsAdvanced->startsWith($sIcon,"glyphicon"))
            {
                if (!$this->StringsAdvanced->startsWith($sIcon,"glyphicon glyphicon")) $sIcon = "glyphicon ". $sIcon;
                return true;
            }

        return false;
    }


    public function insertUploadedImage($sImageSrc='', $sAlt='',$sTitle='', $sDescription='')
    {
        if ($sImageSrc == '' ) return false;

        if ($this->isIcon($sImageSrc)) $newImage = ['src'=>$sImageSrc,'type'=>'icon' ];
        else $newImage = ['src'=>$sImageSrc,'type'=>'upload', 'alt'=>$sAlt, 'title'=>$sTitle, 'description'=>$sDescription];

        //if ($this->checkAndOverwriteExisting($this->arrImages, $newImage,'type') === false)
        $this->removeImagesByType($newImage['type']);
        array_push($this->arrImages,$newImage);

        return true;
    }

    public function insertUploadedCover($sImageSrc='', $sAlt='', $sTitle='', $sDescription='')
    {
        if ($sImageSrc == '') return false;

        $newCover = ['src'=>$sImageSrc,'type'=>'upload', 'alt'=>$sAlt, 'title'=>$sTitle, 'description'=>$sDescription];

        //if ($this->checkAndOverwriteExisting($this->arrCovers, $newCover,'type') === false)
        $this->removeCoversByType($newCover['type']);

        array_push($this->arrCovers,$newCover);

        return true;
    }

    protected function checkAndOverwriteExisting(&$array, $newImage, $sFieldName='src')
    {
        for ($index=0; $index < count($array); $index++) {
            $image = $array[$index];
            if ((isset($image[$sFieldName]))&&($image[$sFieldName] == $newImage[$sFieldName])) {
                $array[$index] = $newImage;
                return $array[$index];
            }
        }

        return false;
    }


    protected function removeImagesArrayByType(&$array, $sTypeToBeRemoved='html')
    {
        foreach ($array as $key => $value)
            if ((isset($array[$key]['type']))&&($array[$key]['type'] == $sTypeToBeRemoved))
            {
                unset($array[$key]);
            }
    }

    protected function removeImagesByType($sTypeToBeRemoved='html')
    {
        $this->removeImagesArrayByType($this->arrImages, $sTypeToBeRemoved);
    }

    protected function removeCoversByType($sTypeToBeRemoved='html')
    {
        $this->removeImagesArrayByType($this->arrCovers, $sTypeToBeRemoved);
    }

}