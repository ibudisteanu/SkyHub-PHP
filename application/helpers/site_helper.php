<?php

if(!function_exists('urlEncodeRedirect'))
{
    function urlEncodeRedirect(&$value)
    {
        if (is_string($value))
        {
            $newStr = '';
            for($index=0; $index<strlen($value); $index++)
            {
                $c = $value[$index];

                if (($c != '/') && ($c != ':'))
                    $c = urlencode($c);

                $newStr .= $c;
            }
            $value = $newStr ;
        }
    }
}
