<?php

require_once APPPATH.'core/models/MY_Advanced_model.php';

class MY_Advanced_multiple_elements_model extends MY_Advanced_model
{
    public $arrFieldsMinimal = array(); //= array("_id","Username","First Name","Last Name","Name","AvatarPicture","Biography","Country","City","Role");
    public $arrMinimalElements = array();

    public function __construct($arrFieldsMinimal)
    {
        parent::__construct();
        $this->arrFieldsMinimal = $arrFieldsMinimal;
    }

    public function findAlreadyElement($sKey="sID",$sValue)
    {
        foreach ($this->arrMinimalElements as $User)
            if ($User->{$sKey} == $sValue)
                return $User;

        return null;
    }

    public function addAlreadyElement($object)
    {
        if ($object != null)
            array_push($this->arrMinimalElements, $object);
    }

    public function loadContainerById($sId, $fields=array(), $bChildren=null, $Sort=array())
    {
        if ($fields = array () ) $fields = $this->arrFieldsMinimal;

        $object = $this->findAlreadyElement("_id",$sId);
        if ($object == null) {
            $this->addAlreadyElement($object);
            $object = parent::loadContainerById($sId, $fields, $bChildren, $Sort);
        }
        return $object;
    }

    public function loadContainerByFieldName($sFieldName, $sFieldValue, $fields=array(), $bChildren=null, $Sort=array())
    {
        if ($fields = array () ) $fields = $this->arrFieldsMinimal;

        $object = $this->findAlreadyElement($sFieldName,$sFieldValue);
        if ($object == null){
            $object = parent::loadContainerByFieldName($sFieldName, $sFieldValue,$fields, $bChildren,$Sort);
            $this->addAlreadyElement($object);
        }
        return $object;
    }

    public function loadContainerByAttachedId($sAttachedId, $fields=array(), $bChildren=null, $Sort=array())
    {
        if ($fields = array () ) $fields = $this->arrFieldsMinimal;

        $object = $this->findAlreadyElement("AttachedParentId",$sAttachedId);
        if ($object == null){
            $object = parent::loadContainerByAttachedId($sAttachedId, $fields, $bChildren, $Sort);
            $this->addAlreadyElement($object);
        }
        return $object;
    }

    public function loadContainerByIdOrFullURL($sID='', $sFullURL='', $fields=array(), $bChildren=null)
    {
        if ($fields = array () ) $fields = $this->arrFieldsMinimal;

        $object = $this->findAlreadyElement("AttachedParentId",$sAttachedId);
        if ($object == null){
            $object = parent::loadContainerByIdOrFullURL($sID, $sFullURL, $fields, $bChildren);
            $this->addAlreadyElement($object);
        }
        return $object;
    }


}