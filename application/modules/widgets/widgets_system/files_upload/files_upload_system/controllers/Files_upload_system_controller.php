<?php

require_once APPPATH.'core/MY_Controller.php';

//TUTORIAL http://talkerscode.com/webtricks/file-upload-progress-bar-using-jquery-and-php.php

class Files_upload_system_controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->BottomScriptsContainer->addScriptResFile(base_url("app/res/js/files-upload-system.js"));
        $this->includeWebPageLibraries('jqueryFileUpload');
    }

    public function showFileUploadContainer($sFileUploadActionName='', $sFileUploadFormId='', $sAcceptedExtensions='', $bEcho=false)
    {
        $data['sFileUploadActionName'] = $sFileUploadActionName;
        $data['sFileUploadFormId'] = $sFileUploadFormId;
        $data['sAcceptedExtensions'] = $sAcceptedExtensions;

        $sContent = $this->renderModuleView('file_upload_view',null,true);

        if ($bEcho) echo $sContent;
        else return $sContent;
    }

    public function unitTestingFileUpload()
    {
        //$this->showFileUploadContainer()
    }

}