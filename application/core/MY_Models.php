<?php

//require_once APPPATH.'core/users/my_user/user/models/UserRoles.php';

class MY_Models extends MY_Model
{
    public $Parent;
    public $arrChildren;
    public $bEnableChildren;
    protected $bChildrenDataInside=false;
    protected $sClassName = "MY_Model"; // Child Name

    public $sAuthorId;
    public $arrInputKeywords = array();

    public function __construct($bEnableChildren=true)
    {
        parent::__construct();
        $this->bEnableChildren = $bEnableChildren;
    }

    public function findAll()
    {
        $Children = $this->findAndLoad(array(),array(),$this->sClassName);
        return $Children;
    }

    public function exists($Id='', $sFullURLLink='', $bEnableChildren=false)
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRead))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for read the request');
            return false;
        }

        if ($Id != '')
            $FindQuery = array ("_id"=>new MongoId($Id));
        else
            if ($sFullURLLink != '')
                $FindQuery = array ("FullURLLink"=>$sFullURLLink);
        else
            return null;

        $cursor = $this->collection->find($FindQuery, array());
        $count = $cursor->count();

        if ($count == 1)
        {
            $obj = new $this->sClassName($bEnableChildren);
            $obj->loadFromCursor($cursor,false);
            return $obj;
        }

        return null;
    }

    public function existsByMongoSearchQuery($MongoSearchQuery, $bEnableChildren=false)
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRead))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for read the request');
            return false;
        }

        $cursor = $this->collection->find($MongoSearchQuery, array());
        $count = $cursor->count();

        if ($count == 1)
        {
            $obj = new $this->sClassName($bEnableChildren);
            $obj->loadFromCursor($cursor,false);
            return $obj;
        }

        return null;
    }

    protected function readCursor($p)
    {
        parent::readCursor($p); // TODO: Change the autogenerated stub

        if (isset($p['InputKeywords'])) $this->arrInputKeywords = $p['InputKeywords'];

        if (isset($p['AuthorId'])) $this->sAuthorId = $p['AuthorId'];

        if (($this->bEnableChildren)&&(isset($p['Children'])))
        {
            $this->arrChildren = $this->processChildren($p['Children'],$this->sClassName);
        }
        else
            $this->arrChildren=[];

        //echo $this->sClassName; if ($this->bEnableChildren==true) var_dump($this->arrChildren);
    }

    public function findAndLoad($Query=array(), $Sort=array(), $class="", $fields=array(),$oneObject=true)
    {
        if ($class == '') $class= $this->sClassName;

        if ($Sort!=[])
            $cursor = $this->collection->find($Query, $fields)->sort($Sort);
        else
            $cursor = $this->collection->find($Query, $fields);

        $count = $cursor->count();
        if ($count > 0)
        {
            //$this->processArray($cursor,$class);

            return $this->processArray($cursor,$class,$oneObject);

        } else
            return null;
    }

    public function processArray($arraySource, $class='', $oneObject=true)
    {
        if ($class == '') $class= $this->sClassName;

        $array = array();
        foreach ($arraySource as $item)
        {
            $itemObject = new $class();
            $itemObject->loadFromCursor($item);
            if (($arraySource->count() ==  1)&&($oneObject==true))
                return $itemObject;
            array_push($array, $itemObject);
        }

        //usort($array, 'sortByOrder');
        return $array;
    }

    public function processChildren($arraySource, $class='')
    {
        if ($class == '') $class= $this->sClassName;
        foreach ($arraySource as $item)
        {
            if (isset($item['_id']))
            {
                $object = new $class(false);
                if (!$this->bChildrenDataInside)  $object->findByMongoId($item['_id']);
                else
                    $object->loadFromCursor($item);
                array_push($this->arrChildren, $object);
            }
        }
        //print_r($this->arrChildren);
        return $this->arrChildren;
    }

    public function getFullURLArray()//this will divide the Full URL in arrays
    {
        $arrayURLs = explode("/",$this->sFullURLLink) ;
        $array = array(); $sURL = '';

        for ($index=0; $index<count($arrayURLs); $index++)
        {
            $URLElement = $arrayURLs[$index];
            $sURL .= $URLElement.'/';
            if ($index==count($arrayURLs)-1) $sURL='';
            if ($sURL != '') $sFinalURL = base_url('category/'.$sURL); else $sFinalURL ='';

            array_push($array,array("url"=>$sFinalURL ,"name"=>$URLElement));
        }
        return $array;
    }

    public function checkOwnership($sUserId = '')
    {
        if ($sUserId == '') $sUserId = $this->MyUser->sID;

        //echo $UserId;
        if ((($sUserId!='')&&($this->sAuthorId == $sUserId))
            ||(($sUserId == $this->MyUser->sID)&&(TUserRole::checkUserRights(TUserRole::Admin))))
            return true;
        else
            return false;
    }

    public function getInputKeywordsToString()
    {
        $result = '';
        for ($index=0; $index<count($this->arrInputKeywords); $index++)
        {
            $result .= $this->arrInputKeywords[$index];
            if ($index != count($this->arrInputKeywords) - 1)
                $result .= ' , ';
        }
        ;
        return $result;
    }

}