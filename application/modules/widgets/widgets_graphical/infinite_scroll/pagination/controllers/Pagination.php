<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pagination extends MY_Controller
{

    private function getPaginationURL($sPaginationSuffix)
    {
        $request = parse_url($_SERVER['REQUEST_URI']);
        $path = $request["path"];

        $result = trim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $path), '/');

        $iPageIndexPosition = strrpos($result,'/page/');
        if (($iPageIndexPosition !== false) && ($iPageIndexPosition > strlen($result)-10))
        {
            $result = substr_replace($result,'',$iPageIndexPosition);
        }


        return rtrim($result,'/').'/'.$sPaginationSuffix;
    }

    public function renderPagination($iIndex, $sPaginationSuffix, $bShowPaginationButtons=false, $bEcho=false)
    {
        $data['index'] = $iIndex;
        $data['href'] = base_url($this->getPaginationURL($sPaginationSuffix));

        $data['bShowPaginationButtons'] = $bShowPaginationButtons;

        $sContent = $this->renderModuleView('pagination_view',$data,true);

        if (!$bEcho) return $sContent;
        else return '';
    }
}