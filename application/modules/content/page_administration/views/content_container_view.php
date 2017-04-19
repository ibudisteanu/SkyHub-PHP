<?php

    $bOK=true; $itemSelected=null; $iIndex=0;
    while ($bOK==true)
    {
        $iIndex++;
        $iMin=10000;
        $bOK=false;

        $itemSelected=null;
        foreach ($arrContent as $item )
            if ($item->bChecked==false)
                if ($iMin > $item->iID)
                {
                    $iMin=$item->iID;
                    $itemSelected=$item;
                }

        if ($itemSelected != null)
        {
            $itemSelected->printObject();
            /*DEBUGGING
            if (($iIndex==4)||($iIndex==5)||($iIndex==6))
            {
                echo 'teeest';
                $itemSelected->printObject();
                echo '/teeest';
            }*/
            $itemSelected->bChecked=true;
            $bOK=true;
            //if ($iIndex==2) return;
        }

    }

//echo 'is fined ******* ';
?>