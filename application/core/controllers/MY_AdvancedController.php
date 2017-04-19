<?php

require_once APPPATH.'core/MY_Controller.php';

class MY_AdvancedController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function normalizeStringForFileNames ($str = '')
    {
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }

    protected function uploadFileForm($sPrefixFileName, $sFormName, $sLocation='uploads/images/', $sFileAllowed="icon|ico|png", $sAlertMsgErrorName='g_msgAddForumError', $sAlertMsgErrorItemName='', $sQueryName='file', $bURLBase=true)
    {
        $sLocation = $this->normalizeStringForFileNames($sLocation);
        $sPrefixFileName = $this->normalizeStringForFileNames($sPrefixFileName);

        $this->load->model('query_trials/query_trials_blocked_upload_file_too_many_times','QueryTrialsBlockedUploadFileTooManyTimes');
        $this->QueryTrialsBlockedUploadFileTooManyTimes->sActionName = $sQueryName;
        $this->QueryTrialsBlockedUploadFileTooManyTimes->sActionValue = 'file_upload';

        if ($sPrefixFileName != '')
            $sLocation = rtrim($sLocation,'/').'/'.rtrim($sPrefixFileName,'/').'/';

        $config['upload_path'] = "./".rtrim($sLocation,'/').'/';

        $config['allowed_types'] = $sFileAllowed;
        $config['overwrite'] = TRUE;

        if (!isset($_FILES[$sFormName]['name']))
        {
            $this->AlertsContainer->addAlert($sAlertMsgErrorName,'error','No <strong>'.$sAlertMsgErrorItemName.'</strong> for upload<br/>');
            return false;
        }

        //getting the file name informations
        $path_info = pathinfo($_FILES[$sFormName]['name']);
        $sImageFileExtension = $path_info['extension'];
        $sImageFileName =  $path_info['filename'];
        $sImageFileDirectory =  $path_info['dirname'];

        $new_name = $this->normalizeStringForFileNames($sImageFileName).'-'.time().'-'.rand().'.'.$this->normalizeStringForFileNames($sImageFileExtension);

        $config['file_name'] = $new_name;

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedUploadFileTooManyTimes))
        {
            $this->AlertsContainer->addAlert($sAlertMsgErrorName,'error',$this->QueryTrials->sError);
            return false;
        }

        if ($sPrefixFileName != '')
        {
            if (!file_exists($sLocation))
                mkdir($sLocation, 0777, true);
        }

        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload($sFormName))
        {
            $this->AlertsContainer->addAlert($sAlertMsgErrorName,'error','Error uploading your <strong>'.$sAlertMsgErrorItemName.'</strong> <br/>'.$this->upload->display_errors());
            return false;
        } else
        {
            $file_data = $this->upload->data();

            $sFileLocation = rtrim($sLocation,'/').'/'.$file_data['file_name'];

            if ($bURLBase)  $sFileLocation = base_url($sFileLocation );

            return $sFileLocation;
        }
    }

    public function avatarImageResize($sImageFile)
    {
        //getting the file name informations
        $path_info = pathinfo($sImageFile);
        $sImageFileExtension = $path_info['extension'];
        $sImageFileName =  $path_info['filename'];
        $sImageFileDirectory =  $path_info['dirname'];

        $this->load->library('AdvancedImage',null,'AdvancedImage');
        $size = getImageSize($sImageFile);

        if ($size[0] < 100)
        {
            throw new Exception('The pictures sent are smaller than 100 pixels');
        }

        if ($size[0] > 1500)
        {
            $this->AdvancedImage->load($sImageFile);
            $this->AdvancedImage->resizeToWidth(1500);
            $this->AdvancedImage->save($sImageFile, $this->AdvancedImage->image_type);

            $size = getImageSize($sImageFile);
        }

        $sNewImageFile = $sImageFile;
        $this->AdvancedImage->load($sNewImageFile);

        if ($size[0] > 300 )
            $this->AdvancedImage->resizeToWidth(300);
        $sNewImageFile=rtrim($sImageFileDirectory,'/').'/'.$sImageFileName.'_300.'.$sImageFileExtension;
        $this->AdvancedImage->save($sNewImageFile, $this->AdvancedImage->image_type);

        $size = getImageSize($sNewImageFile);
        if ($size[0] > 100 )
            $this->AdvancedImage->resizeToWidth(100);
        $sNewImageFile=rtrim($sImageFileDirectory,'/').'/'.$sImageFileName.'_100.'.$sImageFileExtension;
        $this->AdvancedImage->save($sNewImageFile, $this->AdvancedImage->image_type);


        $size = getImageSize($sImageFile);
        if ($size[0] > 50)
            $this->AdvancedImage->resizeToWidth(50);
        $sNewImageFile=rtrim($sImageFileDirectory,'/').'/'.$sImageFileName.'_50.'.$sImageFileExtension;
        $this->AdvancedImage->save($sNewImageFile, $this->AdvancedImage->image_type);

        $size = getImageSize($sImageFile);
        if ($size[0] > 30)
            $this->AdvancedImage->resizeToWidth(30);
        $sNewImageFile=rtrim($sImageFileDirectory,'/').'/'.$sImageFileName.'_30.'.$sImageFileExtension;
        $this->AdvancedImage->save($sNewImageFile, $this->AdvancedImage->image_type);

        return true;
    }

    public function getAvatarOnlineAndResize($sAvatarURL, $sPrefixFileName, $sLocation='uploads/images/', $sFileAllowed="icon|ico|png|jpg|jpeg", $sAlertMsgErrorItemName='image', $sQueryName='file', $bURLBase=true)
    {
        $sPrefixFileName = $this->normalizeStringForFileNames($sPrefixFileName);
        $sLocation = $this->normalizeStringForFileNames($sLocation);

        if ($sFileAllowed != '')
            $sFileAllowed = rtrim($sFileAllowed,'|').'|';

        //getting the file name informations
        $path_info = pathinfo($sAvatarURL);

        $sImageFileName =  $path_info['filename'];
        $sImageFileDirectory =  $path_info['dirname'];

        if (isset($path_info['extension'])) $sImageFileExtension = $path_info['extension'];
        else $sImageFileExtension = 'jpg';

        if ($sFileAllowed != '')
        {
            if (!strpos($sFileAllowed, $sImageFileExtension.'|'))
            {
                echo 'Invalid extensions from the  <strong>'.$sAvatarURL.'</strong> for uploading your '.$sAlertMsgErrorItemName.' on SkyHub<br/>';
                throw new Exception('Invalid extensions from the  <strong>'.$sAvatarURL.'</strong> for uploading your '.$sAlertMsgErrorItemName.' on SkyHub<br/>');
            }
        }

        $notAllowed = array(".", ";",'!','?',"&",'<','=','>',"-", "_","$",":",",","(",")","/","'",'"');
        $sImageFileName = str_replace($notAllowed, '', $sImageFileName );


        $new_name = $this->normalizeStringForFileNames($sImageFileName).'-'.time().'-'.rand().'.'.$this->normalizeStringForFileNames($sImageFileExtension);

        if ($sPrefixFileName != '')
            $sLocation = rtrim($sLocation,'/').'/'.rtrim($sPrefixFileName,'/').'/';

        if ($sPrefixFileName != '')
        {
            if (!file_exists($sLocation))
                mkdir($sLocation, 0777, true);
        }

        $new_name =  rtrim($sLocation,'/').'/'.$new_name;

        /*echo $sLocation.'<br/>';
        echo $new_name.'<br/>';
        echo 'cool1'.$sImageFileName.' '.$sImageFileDirectory.' '.$sImageFileExtension.'  '.$sFileAllowed ;
        die();*/

        if (!file_put_contents($new_name, file_get_contents($sAvatarURL)))
        {
            echo "Couldn't download the '.$sAlertMsgErrorItemName.' from <strong>'.$sAvatarURL.'</strong> when it was trying to upload your new avatar<br/>";
            throw new Exception("Couldn't download the '.$sAlertMsgErrorItemName.' from <strong>'.$sAvatarURL.'</strong> when it was trying to upload your new avatar<br/>");
        }
        try {
            if (!$this->avatarImageResize($new_name))
                return false;
        } catch (Exception $exception)
        {
            echo $exception->getMessage();
            throw $exception;
        }

        if ($bURLBase) $new_name = base_url($new_name);

        return $new_name;
    }

}