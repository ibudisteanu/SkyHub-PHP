<?php

class Voting_controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('voting/votes_model','Votes');

        $this->initializeVotingScript();
    }

    public function initializeVotingScript()
    {
        $this->includeWebPageLibraries('voting');
        $this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/vote-functions.js" : "assets/min-js/voting-functions-min.js"));
    }

    public function renderVotingById($sParentObjectId='', $bHide=true, $sVotingStyleClass='')
    {
        $Vote = $this->Votes->findOrCreateVoteObjectFromParentObjectId($sParentObjectId);
        //$Vote = $this->Votes->findVoteObjectFromParentObjectId($sParentObjectId);

        if ($Vote != null)
        {
            $this->data['Vote'] = $Vote;
            $this->data['sVoteIdName'] = $Vote->sID;
        } else
            $this->data['sVoteIdName'] = 'vote555';

        $this->data['sVotingStyleClass'] = $sVotingStyleClass;

        return $this->renderModuleView('voting_view',$this->data,$bHide);
    }

    public function renderVoting($objVote='', $bHide=true, $sVotingStyleClass='')
    {
        if ($objVote == null)
        {
            throw new Exception('no voting object');
        }

        if (($objVote != null) && (!is_string($objVote)) && (get_class($objVote)=='Vote_model'))
        {
            $this->data['Vote'] = $objVote;
            $this->data['sVoteIdName'] = $objVote->sID;
        } else
            $this->data['sVoteIdName'] = 'vote555';

        $this->data['sVotingStyleClass'] = $sVotingStyleClass;

        return $this->renderModuleView('voting_view',$this->data,$bHide);
    }

}