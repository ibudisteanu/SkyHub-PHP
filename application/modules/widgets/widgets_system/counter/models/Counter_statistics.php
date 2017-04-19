<?php

require_once APPPATH.'core/models/MY_Simple_functions_model.php';

class Counter_statistics extends MY_Simple_functions_model
{

    public $iUsersCount;
    public $iForumsCount;
    public $iTopicsCount;
    public $iCommentsCount;
    public $iContactMessagesCount;

    function __construct()
    {
        parent::__construct();
        $this->initDB('Statistics',TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged,TUserRole::SuperAdmin);

        $this->loadData();
    }

    public function loadData()
    {
        $Data = array ("Name"=>"Counter");

        $cursor = $this->find($Data);

        $count = $cursor->count();
        if ($count == 0)
        {
            {
                $this->createCollection();
                $this->loadData();
            }
        } else
        {
            foreach ($cursor as $p)
            {
                if ($this->readCursor($p) == true) return;
            }
        }
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        if (isset($p["UsersCount"])) $this->iUsersCount = $p["UsersCount"];
        else {
            $this->load->model('users/Users','UsersModel');
            $this->iUsersCount = $this->UsersModel->userCount();
            $this->increaseUsers(0);
        }

        if (isset($p["ForumsCount"])) $this->iForumsCount = $p["ForumsCount"];
        else {
            $this->load->model('forum/Forums_model','ForumsModel');
            $this->iForumsCount = $this->ForumsModel->dataCount();
            $this->increaseForums(0);
        }

        if (isset($p["CommentsCount"])) $this->iCommentsCount = $p["CommentsCount"];
        else {
            $this->load->model('reply/Replies_model','RepliesModel');
            $arrReplies = $this->RepliesModel->findAllReplies();
            $this->iCommentsCount=0;
            foreach ($arrReplies as $reply)
                $this->iCommentsCount+=$reply->arrChildrenCount;

            $this->increaseComments(0);
        }


        if (isset($p["TopicsCount"])) $this->iTopicsCount = $p["TopicsCount"];
        else{
            $this->load->model('forum/Forums_model','ForumsModel');
            $arrForums = $this->ForumsModel->findAllForums();
            $this->iTopicsCount=0;
            foreach ($arrForums as $forum) {
                $forum->getCategories();
                foreach ($forum->arrCategories as $category) {
                    $category->getTopics();
                    $this->iTopicsCount += count($category->arrTopics);
                }
            }
            $this->increaseTopics(0);
        }

        if (isset($p["ContactMessagesCount"]))
            $this->iContactMessagesCount = $p["ContactMessagesCount"];

        if ((isset($p["UsersCount"])) || (isset($p["ForumsCount"])) || (isset($p["CommentsCount"])) || (isset($p["TopicsCount"])))  return true;
        else return false;
    }

    public function increaseUsers($iNo=1)
    {
        $MongoData =array("UsersCount"=>$this->iUsersCount+$iNo);
        $this->update(array ("Name"=>"Counter"), $MongoData,'$set',true);
    }

    public function increaseContactMessages($iNo=1)
    {
        $MongoData =array("ContactMessagesCount"=>$this->iContactMessagesCount+$iNo);
        $this->update(array ("Name"=>"Counter"), $MongoData,'$set',true);
    }

    public function increaseForums($iNo=1)
    {
        $MongoData = array("ForumsCount"=>$this->iForumsCount+ $iNo);
        $this->update(array ("Name"=>"Counter"), $MongoData,'$set',true);
    }

    public function increaseComments($iNo=1)
    {
        $MongoData =array("CommentsCount"=>$this->iCommentsCount + $iNo);
        $this->update(array ("Name"=>"Counter"), $MongoData,'$set',true);
    }

    public function increaseTopics($iNo=1)
    {
        $MongoData = array("TopicsCount"=>$this->iTopicsCount+ $iNo);
        $this->update(array ("Name"=>"Counter"), $MongoData,'$set',true);
    }

    private function createCollection()
    {
        $MongoData = array ("Name"=>"Counter");

        $this->collection->insert($MongoData);
    }


}