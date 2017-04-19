<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Advanced_model.php';
require_once APPPATH.'modules/widgets/chat/conversations/message/models/Message_model.php';

class Conversation_model extends MY_Advanced_model
{
    public $sClassName = 'Conversation_model';

    public $arrAuthors = [];
    public $arrMessages = [];

    //store in the Base

    //public $dtCreationDate
    //public $dtLastChangeDate

    public function __construct()
    {

        parent::__construct(true);

        $this->initDB('Conversations',TUserRole::notLogged,TUserRole::notLogged,TUserRole::User,TUserRole::User);

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->load->library('TimeLibrary',null,'TimeLibrary');
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p,$bEnableChildren);

        if (isset($p["Authors"])) $this->arrAuthors = $p["Authors"];
        if (isset($p["Messages"]))
        {
            $arrMessagesCursor = $p["Messages"];
            foreach ($arrMessagesCursor as $messageCursor)
            {
                $message = new Message_model();
                $message->readCursor($messageCursor);

                array_push($this->arrMessages, $message);
            }
        }

        //$this->AlertsContainer->addAlert('g_msgGeneralSuccess','success','Logged in successfully');
    }

    protected function serializeProperties()
    {
        $this->sAuthorId = '';

        $arrResult = parent::serializeProperties();

        if ((isset($this->arrAuthors)))
        {
            sort($this->arrAuthors);

            $arrResult = array_merge($arrResult, array("Authors"=>$this->arrAuthors));
        }

        if ((isset($this->arrMessages)))
        {
            $arrMessagesSerialized = [];
            foreach ($this->arrMessages as $message)
                array_push($arrMessagesSerialized, $message->serializeProperties());

            $arrResult = array_merge($arrResult, array("Messages"=>$arrMessagesSerialized));
        }

        return $arrResult;
    }

    public function getConversationTitle()
    {
        $this->load->model('users/users_minimal','UsersMinimal');

        $sTitle = '';
        $iCount = 0;

        foreach ($this->arrAuthors as $author)
        {
            if ($author != $this->MyUser->sID) {
                $User = $this->UsersMinimal->userByMongoId($author);

                if ($User != null)
                {
                    $sTitle .= ($User->getFullName() != '' ? $User->getFullName() : $User->sUsername).' ,';
                }

                $iCount++;
            }
            if ($iCount > 5) break;
        }

        if ($sTitle == '')
            $sTitle .= ($this->MyUser->getFullName() != '' ? $this->MyUser->getFullName() : $this->MyUser->sUsername);

        $sTitle = rtrim($sTitle,',');

        return $sTitle;
    }

    public function getRecentlyMessages($dtDate, $iNumberOfMessages=10)
    {
        if (is_array($dtDate))
            $dtDate = new MongoDate($dtDate['sec'],$dtDate['usec']);

        $result = [];
        $bRecentlyMessages=true; $iMessagesIndex = count($this->arrMessages)-1;
        while (($bRecentlyMessages)&&($iMessagesIndex>=0))
        {
            $message = $this->arrMessages[$iMessagesIndex];
           /* var_dump($dtDate);
            var_dump($message->dtCreationDate);*/

            if ($message->dtCreationDate->sec > $dtDate->sec) array_push($result,$message);
            else $bRecentlyMessages=false;

            $iMessagesIndex--;
        }

        return $result;
    }

    public function getEarlierMessages($dtDate, $iNumberOfMessages=10)
    {
        if (is_array($dtDate))
            $dtDate = new MongoDate($dtDate['sec'],$dtDate['usec']);

        $result = [];
        $iMessagesIndex = count($this->arrMessages)-1;
        while (($iMessagesIndex>=0)&&(count($result) < $iNumberOfMessages))
        {
            $message = $this->arrMessages[$iMessagesIndex];

            if ($message->dtCreationDate->sec < $dtDate->sec) array_push($result,$message);
            $iMessagesIndex--;
        }

        /*var_dump($dtDate);
        var_dump($this->arrMessages);*/

        return $result;
    }

    public function getLastMessagesWrittenByAuthor($sAuthor='', $iMaxCount=3)
    {
        if ($sAuthor == '') $sAuthor= $this->MyUser->sID;

        $result = [];

        $index =  count($this->arrMessages)-1;
        while (($index >= 0)&&(count($result) < $iMaxCount))
        {
            $message = $this->arrMessages[$index];
            if ($message->sAuthorId == $sAuthor)
            {
                array_push($result, $message);
            }
            $index--;
        }

        return $result ;
    }

    public function getChatMessagesInitializationDates($iNumberOfMessages=10)
    {
        if (count($this->arrMessages) > 0)
        {
            $iPosition = count($this->arrMessages)-$iNumberOfMessages;
            if ($iPosition < 0) $iPosition =0;

            return ['dtLastMessageDate'=>$this->arrMessages[count($this->arrMessages)-1]->dtCreationDate,'dtFirstMessageDate'=>$this->arrMessages[$iPosition]->dtCreationDate];

        } else
            return ['dtLastMessageDate'=>new MongoDate(0,0),'dtFirstMessageDate'=>new MongoDate()];
    }

    public function getLastMessageDateChat($arrMsg=null)
    {
        if (($arrMsg == null)&&(!is_array($arrMsg))) {
            if (count($this->arrMessages) > 0) return $this->arrMessages[count($this->arrMessages) - 1]->dtCreationDate;
            else return new MongoDate(0, 0);
        } else
            if (count($arrMsg) > 0)
            {
                $dtMax=$arrMsg[0]->dtCreationDate;
                for ($i=0; $i<count($arrMsg); $i++)
                    if ($dtMax->sec < $arrMsg[$i]->dtCreationDate->sec)
                        $dtMax = $arrMsg[$i]->dtCreationDate;

                return $dtMax;
            }
            else return new MongoDate(0, 0);
    }

    public function getFirstMessageDateChat($arrMsg=null)
    {
        if (($arrMsg == null)&&(!is_array($arrMsg))) {
            if (count($this->arrMessages) > 0) return $this->arrMessages[0]->dtCreationDate;
            else return new MongoDate();
        } else
            if (count($arrMsg) > 0)
            {
                $dtMin=$arrMsg[0]->dtCreationDate;
                for ($i=0; $i<count($arrMsg); $i++)
                    if ($dtMin->sec > $arrMsg[$i]->dtCreationDate->sec) {
                        $dtMin = $arrMsg[$i]->dtCreationDate;
                    }

                return $dtMin;
            }
            else return new MongoDate();
    }

}
