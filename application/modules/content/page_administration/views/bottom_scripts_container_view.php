<?php

    foreach ($arrContent as $item )
        if ($item->sScriptName == 'WebLibrary')
            $item->printObject();

    foreach ($arrContent as $item )
        if (!$item->bPrintedAlready)
            $item->printObject();

?>