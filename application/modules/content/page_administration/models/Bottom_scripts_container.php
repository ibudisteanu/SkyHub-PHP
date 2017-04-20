<?php

require_once APPPATH.'modules/content/page_administration/models/Bottom_script_object.php';

class Bottom_scripts_container extends CI_Model
{
    public $arrContent=[];//Bottom_script_object

    public function __construct()
    {
        parent::__construct();
    }

    public function addScript($sText, $bAddScriptTag=false, $sScriptName='none',  $bFirst=false)
    {
        if (strlen($sText) > 0)
        {
            //check if already exists
            if (!$this->checkScriptAlreadyExists($sText))
            {
                if ($bAddScriptTag)
                    $sText = '<script type="text/javascript">'.$sText."</script>";

                $ScriptObject = new Bottom_script_object($sText,$sScriptName);

                if ($bFirst)
                    array_unshift($this->arrContent, $ScriptObject);
                else
                   array_push($this->arrContent,$ScriptObject);
            }
        }
    }

    public function addScriptResFile($sFileLocation, $sScriptName='none', $bFirst=false)
    {
        if (strlen($sFileLocation) > 0)
        {
            $sText = '<script type="text/javascript" src="'.$sFileLocation.'"></script>';

            //check if already exists
            if (!$this->checkScriptAlreadyExists($sText))
            {
                $ScriptObject = new Bottom_script_object($sText, $sScriptName);

                if ($bFirst)
                    array_unshift($this->arrContent, $ScriptObject);
                else
                    array_push($this->arrContent,$ScriptObject);
            }
        }
    }

    protected function checkScriptAlreadyExists($sText)
    {
        foreach ($this->arrContent as $object)
            if ($object->sText == $sText)
                return true;

        return false;
    }

    private function deleteScriptTag($sText)
    {
        if ((strpos($sText,'<') !== false)&&(strpos($sText,'>') !== false)) {
            $sText = substr($sText, strpos($sText, '>'));

            $index = strlen($sText)-1;
            while (($index > 0 )&&($sText[$index] != '<'))
                $index--;

            $sText = substr($sText,1,$index-1);
        }

        return $sText;
    }

    public function findScriptByName($sScriptName, $bDeleteScriptTag=false)
    {
        $result = '';
        for ($index=0; $index<count($this->arrContent); $index++)
            if ($this->arrContent[$index]->sScriptName == $sScriptName)
                $result .= $this->deleteScriptTag($this->arrContent[$index]->sText);

        return $result;
    }

    public function renderView()
    {
        $data['arrContent']=$this->arrContent ;
        $this->load->view('page_administration/bottom_scripts_container_view',$data);
    }






    protected function insertWebLibrary($sScript='')
    {
        $this->addScript($sScript,'','WebLibrary');
    }

    //functions for library
    public function addWebLibrary($sWebLibrary='')
    {

        if ($sWebLibrary == 'advanced-text-editor') //https://api.github.com/emojis
        {
            $this->insertWebLibrary('
            <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">
            <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.js"></script>');

            /*
            <script>document.emojiSource = "'.base_url('assets/emoji/summernote-ext-emoji-master/pngs/').'"; </script>
            <script src="'.base_url('assets\emoji\summernote-ext-emoji-master\dist\summernote-ext-emoji-min.js').'"></script
            */

            $this->insertWebLibrary('
            <script src="'.base_url('assets/emoji2/summernote-emoji.js').'"></script>
            <link href="'.base_url('assets/emoji2/summernote-emoji.css').'" rel="stylesheet">');

            //echo "<script>$.ajax({ url: 'https://api.github.com/emojis', async: false }).then(function(data) {window.emojis = Object.keys(data);alert(Object.keys(data)); window.emojiUrls = data;}); </script>";
            //echo "<script>$.ajax({ url: '".base_url('assets/emoji/emojis')."', async: false }).then(function(data) {window.emojis = Object.keys(data); alert(Object.keys(data));window.emojiUrls = data;}); </script>";

            $this->insertWebLibrary('<script>$.ajax({ url: "https://api.myjson.com/bins/2fb13", async: false }).then(function(data) {window.emojis = Object.keys(data); window.emojiUrls = data;}); </script>');
        } else

        if ($sWebLibrary == 'tooltip')
        {
            $this->insertWebLibrary('
            <link rel="stylesheet" href="'.base_url('assets/tooltips/advanced_tooltips.css').'">
            <script src="'.base_url('assets/tooltips/advanced_tooltips.js').'"></script>');
            //echo "<script>$(document).ready(function(){ $('[data-toggle=\"tooltip\"]').tooltip(); $('[data-toggle=\"tooltip-success\"]').tooltip(); $('[data-toggle=\"tooltip-error\"]').tooltip();});</script>";
        } else

        if ($sWebLibrary == 'validation') {
            $this->insertWebLibrary('<script src="'.base_url('assets/validation/advanced-validation.js').'"></script>');
        } else
        if ($sWebLibrary == 'jqueryFileUpload'){
            $this->insertWebLibrary('
            <link rel="stylesheet" href="http://blueimp.github.io/jQuery-File-Upload/css/jquery.fileupload.css">
            <link rel="stylesheet" href="http://blueimp.github.io/jQuery-File-Upload/css/jquery.fileupload-ui.css">
            <link rel="stylesheet" href="http://blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
            <!-- CSS adjustments for browsers with JavaScript disabled -->
            <noscript><link rel="stylesheet" href="https://blueimp.github.io/jQuery-File-Upload/css/jquery.fileupload-noscript.css"></noscript>
            <noscript><link rel="stylesheet" href="https://blueimp.github.io/jQuery-File-Upload/css/jquery.fileupload-ui-noscript.css"></noscript>
            <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
            <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
            <script src="https://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
            <script src="https://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload-process.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload-image.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload-audio.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload-video.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload-validate.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload-ui.js"></script>
            <script src="https://blueimp.github.io/jQuery-File-Upload/js/main.js"></script>');
        }
        else
        if ($sWebLibrary == 'advanced-functions')
        {
            $this->insertWebLibrary('<script src="'.base_url('assets/functions/advanced-functions.js').'"></script>');
        } else

        if ($sWebLibrary == 'advanced-tooltip')
        {
            /*
            echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/css/themes/tooltipster-punk.css" />';
            echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/js/jquery.tooltipster.min.js"></script>';
            echo  "<script>$(document).ready(function() { $('.tooltip').tooltipster();});</script>";
            */
        } else
        if ($sWebLibrary == 'jquery.form'){
            $this->insertWebLibrary('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.1/jquery.form.min.js"></script>');
        }
        else


        if ($sWebLibrary=='voting')
        {
            $this->insertWebLibrary('
            <script src="'.base_url('assets/upvote/jquery.upvote.js').'"></script>
            <link rel="stylesheet" href="'.base_url('assets/upvote/jquery.upvote.css').'">');

            //echo $this->load->view('voting/voting_javascript_function',null);
        } else

        if ($sWebLibrary == 'country-select') {
            $this->insertWebLibrary('
            <link rel="stylesheet" href="' . base_url('theme/assets/country-select/css/countrySelect.min.css') . '">
            <link rel="stylesheet" href="' . base_url('theme/assets/country-select/css/demo.css') . '">');

            $this->insertWebLibrary('<script src="' . base_url('theme/assets/country-select/js/countrySelect.min.js') . '"></script>');
        } else

        if ($sWebLibrary == 'data-masonry') {
            $this->insertWebLibrary('
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/3.0.4/jquery.imagesloaded.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>');

        } else

        if ($sWebLibrary == 'file-style') {
            $this->insertWebLibrary('<script type = "text/javascript" src = "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-filestyle/1.2.1/bootstrap-filestyle.min.js"> </script>');

        }
    }

}