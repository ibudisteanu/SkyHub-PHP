<?php

class Query_trials extends MY_Model
{
       public $sError;
    public $iAttemptsRemaining;
    public $iAttempts;

    //must be set before you use other methods
    protected $QueryCase;

    public function __construct()
    {
        parent::__construct();
        $this->initDB('Trials',TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged);
    }

    function checkIPAddress($QueryCase)
    {
        $this->QueryCase = $QueryCase;
        $this->iAttempts=$this->getAttempts();
        $this->iAttemptsRemaining = $this->QueryCase->iActionMaxTrials - $this->iAttempts;

        if ($this->iAttempts < $this->QueryCase->iActionMaxTrials)
            return true;
        else
        {
            $this->sError=$QueryCase->sActionBlockedMessage.' <strong>'.gmdate("H:i:s", $this->QueryTrials->getRemainingBlockedTime()).' s </strong>';
            return false;
        }
    }

    //returns how many logins attemps were found in the database to this database
    protected function getAttempts()
    {
        $this->load->model('ip/ip','IP');

        $sTimeSeconds = $this->QueryCase->iActionTimeSec;

        $TimeNow = new MongoDate(strtotime("now"));
        $TimeEarly = new MongoDate(strtotime("now")-$sTimeSeconds);
        $MongoData = array ("IP"=>$this->IP->sIP,"Action"=>$this->QueryCase->sActionName,"Value"=>$this->QueryCase->sActionValue,"Time"=>array('$lte'=>$TimeNow,'$gte'=>$TimeEarly));

        $cursor = $this->collection->find($MongoData);

        return $cursor->count();
    }

    //Increase number of attempts. Set last login attempt if required.
    public function addAttempt()
    {
        $this->load->model('ip/ip','IP');

        $this->removeOldAttempts();

        $Time = new MongoDate(strtotime("now"));
        $MongoData = array ("IP"=>$this->IP->sIP,"Time"=>$Time,"Action"=>$this->QueryCase->sActionName,"Value"=>$this->QueryCase->sActionValue);

        $this->insertData($MongoData);

        if ($this->iAttemptsRemaining > 1)
            $this->sError .= $this->QueryCase->sActionWrongMessage.' '.($this->iAttemptsRemaining-1)." remaining attempts";
        else
        {
            $this->getRemainingBlockedTime();
        }
    }

    public function removeOldAttempts()
    {
        $this->load->model('ip/ip','IP');

        $sTimeSeconds = $this->QueryCase->iActionTimeSec;

        $TimeEarly = new MongoDate(strtotime("now")-$sTimeSeconds);

        $MongoData = array ("IP"=>$this->IP->sIP,"Action"=>$this->QueryCase->sActionName,"Value"=>$this->QueryCase->sActionValue,"Time"=>array('$lt'=>$TimeEarly));
        $this->collection->remove($MongoData);
    }

    public function removeAllAttempts()
    {
        $this->load->model('ip/ip','IP');

        $MongoData = array ("IP"=>$this->IP->sIP,"Action"=>$this->QueryCase->sActionName,"Value"=>$this->QueryCase->sActionValue);
        $this->collection->remove($MongoData);
    }

    public function getRemainingBlockedTime()
    {
        $this->load->model('ip/ip','IP');

        $TimeNow = new MongoDate(strtotime("now"));
        $TimeEarly = new MongoDate(strtotime("now") - $this->QueryCase->iActionTimeSec);

        $MongoData = array("IP" => $this->IP->sIP, "Action" => $this->QueryCase->sActionName, "Value" => $this->QueryCase->sActionValue, "Time" => array('$gte' => $TimeEarly));
        $cursor = $this->collection->find($MongoData);

        if ($cursor->count() == 0) return 0;

        $initialMin=1000000;
        $min = $initialMin;
        foreach ($cursor as $p)
        {
            $date = $p["Time"];

            $diff = $TimeNow->sec - $date->sec;
            if ($min > $diff)
            {
                $min=$diff;
            }
        }

        if ($min != $initialMin)
        {
            $time = $this->QueryCase->iActionTimeSec-$min;
            $this->sError .= $this->QueryCase->sActionBlockedStartedMessage. '<strong>'. gmdate("H:i:s", $time)." s </strong>";
            return $time;

        } else
            return 0;
    }

}