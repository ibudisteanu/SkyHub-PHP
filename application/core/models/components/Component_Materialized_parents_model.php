<?php

class MaterializedDataInherit { // tutorial https://docs.mongodb.com/manual/tutorial/model-tree-structures/

    public $sParentClassName = '';
    public $sParentInheritPropertyName = '';

    public $ProcessObject;
    public $sProcessObjectPropertyName;

    public function __construct($sParentClassName, $sParentInheritPropertyName, $ProcessObject, $sProcessObjectPropertyName )
    {
        $this->sParentClassName = $sParentClassName;
        $this->sParentInheritPropertyName = $sParentInheritPropertyName;

        $this->ProcessObject = $ProcessObject;
        $this->sProcessObjectPropertyName = $sProcessObjectPropertyName;
    }
}

class MaterializedParentsModel
{
    protected  $currentObject;

    protected $arrMaterializedDataInherit = [];

    public function __construct($currentObject)
    {
        $this->currentObject = $currentObject;
    }

    public function defineMaterializedData($sParentClassName, $sParentInheritPropertyName, $ProcessObject, $sProcessObjectPropertyName)
    {
        if ($ProcessObject == null) $ProcessObject = $this->currentObject;

        $newData = new MaterializedDataInherit($sParentClassName, $sParentInheritPropertyName, $ProcessObject, $sProcessObjectPropertyName);
        array_push($this->arrMaterializedDataInherit, $newData);
        return $newData;
    }

    public function clearMaterializedData()
    {
        $this->arrMaterializedDataInherit = [];
    }

    public function searchMaterializedParentElement($sParentID='', $materializedData)
    {
        if (is_object($sParentID)) $sParentID = $sParentID->sID;

        $iPos = strpos($materializedData->ProcessObject->{$materializedData->sProcessObjectPropertyName},$sParentID);
        if ($iPos !== false)
            return $iPos;

        return false;
    }

    protected function addMaterializedParentElement($parent, $bIncludeParentGrandparents=true, $bIncludeParentID=true)
    {
        if ((!is_string($parent))&&(is_object($parent))) $sID = $parent->sID;
        else $sID = (string) $parent;

        foreach ($this->arrMaterializedDataInherit as $data)
            if (get_class($parent) == $data->sParentClassName)
            {
                //my parent + all grand-parents of my parent
                if ($bIncludeParentID) $allParentAndGrandParents = [$sID];
                else $allParentAndGrandParents = [];

                if (($bIncludeParentGrandparents)&&(isset($parent->{$data->sParentInheritPropertyName}))) {
                    $parentElements = explode(",",$parent->{$data->sParentInheritPropertyName});
                    $allParentAndGrandParents = array_merge($allParentAndGrandParents,$parentElements);
                }

                foreach ($allParentAndGrandParents as $element)
                    if ($element != ''){
                        $iPos = $this->searchMaterializedParentElement($element, $data);
                        if ($iPos === false)
                            $data->ProcessObject->{$data->sProcessObjectPropertyName } = rtrim($data->ProcessObject->{$data->sProcessObjectPropertyName }, ',') . ',' . $element . ',';
                    }
            }
    }

    public function addMaterializedParent($parent, $bIncludeParentGrandparents=true, $bIncludeParentID=true)
    {
        if (is_array($parent))
            foreach ($parent as $element)
                $this->addMaterializedParentElement($element, $bIncludeParentGrandparents, $bIncludeParentID);
        else
            $this->addMaterializedParentElement($parent, $bIncludeParentGrandparents, $bIncludeParentID);
    }

    protected function removeMaterializedParentElement($parent, $bDelete=true, $bRemoveParentId=true)
    {
        $sID = $parent->sID;

        foreach ($this->arrMaterializedDataInherit as $data)
            if (get_class($parent) == $data->sParentClassName)
            {

                //my parent + all grand-parents of my parent
                if ($bRemoveParentId)  $allParentAndGrandParents  = [$sID];
                else $allParentAndGrandParents = [];

                if (($bDelete)&&(isset($parent->{$data->sParentInheritPropertyName}))) {
                    $parentElements = explode(",",$parent->{$data->sParentInheritPropertyName});
                    $allParentAndGrandParents = array_merge($allParentAndGrandParents,$parentElements);
                }

                foreach ($allParentAndGrandParents  as $element)
                    if ($element != ''){
                        $iPos = $this->searchMaterializedParentElement($element, $data);
                        if ($iPos !== false) {
                            $data->ProcessObject->{$data->sProcessObjectPropertyName} = str_replace($element . ',', "", $data->ProcessObject->{$data->sProcessObjectPropertyName});

                            if ($data->ProcessObject->{$data->sProcessObjectPropertyName} == ',')
                                $data->ProcessObject->{$data->sProcessObjectPropertyName} = null;
                        }
                    }
            }
    }

    public function clearMaterializedParent()
    {
        foreach ($this->arrMaterializedDataInherit as $data)
        {
            if (isset($data->ProcessObject->{$data->sProcessObjectPropertyName}))
                $data->ProcessObject->{$data->sProcessObjectPropertyName} = '';
        }
    }

    public function removeMaterializedParent($parent, $bDelete=true, $bRemoveParentId=true)
    {
        if (is_array($parent))
            foreach ($parent as $element)
                $this->removeMaterializedParentElement($element, $bDelete, $bRemoveParentId);
        else
            $this->removeMaterializedParentElement($parent, $bDelete, $bRemoveParentId);
    }

    public function refreshChild($ProcessObject, $data, $bDelete, $bRemoveParentId=false, $bInsert=true, $bIncludeParentID)
    {
        if ($ProcessObject == null) return false;

        $this->arrMaterializedDataInherit = [];
        $this->defineMaterializedData($data->sParentClassName,$data->sParentInheritPropertyName,$ProcessObject,$data->sProcessObjectPropertyName);

        if (($bDelete) || ($bRemoveParentId))
            $this->removeMaterializedParent($this->currentObject, $bDelete, $bRemoveParentId);

        if (($bIncludeParentID) || ($bInsert))
            $this->addMaterializedParent($this->currentObject, $bInsert, $bIncludeParentID );
    }

    public $arrMaterializedDataInheritedForUpdate;
    public function refreshChildren($bDelete, $bRemoveParentId=false, $bInsert=true, $bIncludeParentID=false,  $bLocalStore=false, $bUpdateDB=false)
    {
        $backup = $this->arrMaterializedDataInherit;

        if ($this->arrMaterializedDataInheritedForUpdate == null) {
            $this->arrMaterializedDataInheritedForUpdate = $this->currentObject->getMaterializedDataInheritedForUpdate();
        }

        foreach ($this->arrMaterializedDataInheritedForUpdate as $data)
        {
            if (is_array($data->ProcessObject))
            {
                foreach ($data->ProcessObject as $ProcessObject)
                    $this->refreshChild($ProcessObject, $data, $bDelete, $bRemoveParentId, $bInsert, $bIncludeParentID);
            } else
                $this->refreshChild($data->ProcessObject, $data, $bDelete, $bRemoveParentId, $bInsert, $bIncludeParentID);
        }


        if ($bUpdateDB)
            foreach ($this->arrMaterializedDataInheritedForUpdate  as $data)
                if (is_array($data->ProcessObject))
                {
                    foreach ($data->ProcessObject as $ProcessObject) {
                        if ($ProcessObject != null)
                            try {
                                $ProcessObject->storeUpdate();
                            } catch (Exception $exception){
                                echo 'Error Refreshing Materialized Object';
                                var_dump($ProcessObject);
                            }
                    }
                } else
                    if ($data->ProcessObject != null)
                        $data->ProcessObject->storeUpdate();

        if (!$bLocalStore)
            $this->arrMaterializedDataInheritedForUpdate = [];

        $this->arrMaterializedDataInherit = $backup;
    }

}