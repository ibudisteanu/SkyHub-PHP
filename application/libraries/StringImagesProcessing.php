<?php

class StringImagesProcessing
{

    public function __construct()
    {
        $sEmojisJSON = file_get_contents (APPPATH.'../assets/emoji/emojis');
        $this->arrEmojisDots = json_decode($sEmojisJSON);
        //var_dump($this->arrEmojisDots);

        $sEmojisJSON = file_get_contents (APPPATH.'../assets/emoji/emojis_2');
        $this->arrEmojisAll = json_decode($sEmojisJSON);
        //var_dump($this->arrEmojisAll);

        $this->UnitTesting();
    }


    public function removeEmojisImages(&$sText)
    {
        $posTag = strpos($sText, 'emoji="enabled"');

        while ($posTag > 0)
        {
//            echo $posTag;
//            echo $sText.'<br/><br/>';

            $posImageTag = $posTag;
            $posImageTagEnd = $posTag;
            while (($posImageTagEnd+1<strlen($sText))&&($sText[$posImageTagEnd]!='>')) {
                $posImageTagEnd++;
            }


            $sEmojiCode = '';
            $sSubstring = ''; $bFoundImageTag=false;
            while ( $posImageTag > 0)
            {
                $sSubstring = $sText[$posImageTag] . $sSubstring;
                //echo 'Substring'.$sSubstring.'#';
                if (strpos($sSubstring,'<img src="') != 0) {
                    $iImageSrcEnd = 11;

                    while (($iImageSrcEnd+1<strlen($sSubstring))&&($sSubstring[$iImageSrcEnd]!='"')) {
                        $iImageSrcEnd++;
                    }
                    if ($iImageSrcEnd > 6) {
                        $sEmojiImage = substr($sSubstring, 11, $iImageSrcEnd - 11);
                        //echo '@'.$sEmojiImage.'@';

                        if ($sEmojiImage != '') {
                            foreach ($this->arrEmojisDots as $EmojiItemCode => $EmojiItemUrl )
                                if ($sEmojiImage == $EmojiItemUrl)
                                {
                                    $sEmojiCode = ':'.$EmojiItemCode;
                                    $bFoundImageTag=true;
                                    break;
                                }

                            if ($bFoundImageTag) break;

                            foreach ($this->arrEmojisAll as $EmojiItemCode => $EmojiItemUrl )
                                if ($sEmojiImage == $EmojiItemUrl)
                                {
                                    $sEmojiCode = $EmojiItemCode;
                                    $bFoundImageTag=true;
                                    break;
                                }

                            //echo $sText;

                            if ($bFoundImageTag) break;

                        }
                    }
                    break;
                }
                $posImageTag--;
            }

            //no emoticon image tag found in the text
            if (!$bFoundImageTag)  break;

            //echo '   '.$posImageTag. ' '.$posImageTagEnd;
            //echo $sEmojiImage;
            $str1=substr($sText,0,$posImageTag+1);
            $str2=substr($sText,$posImageTagEnd+1,strlen($sText));
            //echo $str1.'###'.$str2.'       ';
            $strEmojiCode = '#EmOjI25@'.$sEmojiCode.'#$';
            $sText = $str1 .$strEmojiCode. $str2;

            $posTag = strpos($sText, 'emoji="enabled"', strlen($str1.$strEmojiCode));
        }
        //echo $sText;
    }

    public function renderEmojisImages(&$sText)
    {
        $posTag = strpos($sText, '#EmOjI25@');

        while ($posTag > 0)
        {
            $posCodeTag = $posTag;
            $posCodeTagEnd = $posTag+9;

            $sEmojiCode = ''; $sEmojiImage='';
            $sSubstring = ''; $bFoundEmojiTag=false; $bFoundImage=false;
            while ( $posCodeTagEnd < strlen($sText))
            {
                $sSubstring = $sSubstring.$sText[$posCodeTagEnd];
                if (strpos($sSubstring, '#$') != 0) {
                    $sEmojiCode = substr($sSubstring,0,strlen($sSubstring)-2);

                    //echo 'cool#'.$sEmojiCode.'#';
                    $bFoundEmojiImage=false;
                    if ($sEmojiCode != '') {
                        foreach ($this->arrEmojisDots as $EmojiItemCode => $EmojiItemUrl)
                            if ($sEmojiCode == ':'.$EmojiItemCode) {
                                $sEmojiImage = $EmojiItemUrl;
                                $bFoundEmojiImage = true;
                                break;
                            }
                        if ($bFoundEmojiImage) break;

                        foreach ($this->arrEmojisAll as $EmojiItemCode => $EmojiItemUrl)
                            if ($sEmojiCode == $EmojiItemCode) {
                                $sEmojiImage = $EmojiItemUrl;
                                $bFoundEmojiImage = true;
                                break;
                            }

                        if ($bFoundEmojiImage) break;
                    }

                }

                $posCodeTagEnd++;
            }

            //no emoticon image tag found in the text
            if ((!$bFoundEmojiImage) )  break;

            $str1=substr($sText,0,$posCodeTag);
            $str2=substr($sText,$posCodeTagEnd+1,strlen($sText));
            //echo $str1.'###'.$str2.'       ';
            $strEmojiCode = '<img src="'.$sEmojiImage.'" emoji="enabled" style="max-height:16px;">';
            $sText = $str1 .$strEmojiCode. $str2;


            //echo $sEmojiCode;
            $posTag = strpos($sText, '#EmOjI25@',strlen($str1.$strEmojiCode));
        }
    }

    private function processHTMLForImages($sText)
    {

        preg_match_all('/<img[^>]+>/i', $sText, $result);

        if ((is_array($result))&&(count($result) > 0)&&(is_array($result[0])))
            $result = $result[0];

        //var_dump($result);

        $arrImagesSources = array();
        for ($index=0; $index < count($result); $index++)
        {
            $img_tag = $result[$index];
            $element = null;
            preg_match_all('/(alt|title|src|class)=("[^"]*")/i',$img_tag, $element);

            if ($element != null)
                array_push($arrImagesSources, $element);

        }

        /*
            [<img src="/Content/Img/stackoverflow-logo-250.png" width="250" height="70" alt="logo link to homepage" />] => Array
            (
                [0] => Array
                    (
                        [0] => src="/Content/Img/stackoverflow-logo-250.png"
                        [1] => alt="logo link to homepage"
                    )

                [1] => Array
                    (
                        [0] => src
                        [1] => alt
                    )

                [2] => Array
                    (
                        [0] => "/Content/Img/stackoverflow-logo-250.png"
                        [1] => "logo link to homepage"
                    )

            )

            [<img class="vote-up" src="/content/img/vote-arrow-up.png" alt="vote up" title="This was helpful (click again to undo)" />] => Array
            (
                [0] => Array
                    (
                        [0] => src="/content/img/vote-arrow-up.png"
                        [1] => alt="vote up"
                        [2] => title="This was helpful (click again to undo)"
                    )

                [1] => Array
                    (
                        [0] => src
                        [1] => alt
                        [2] => title
                    )

                [2] => Array
                    (
                        [0] => "/content/img/vote-arrow-up.png"
                        [1] => "vote up"
                        [2] => "This was helpful (click again to undo)"
                    )

            )
         */

        $arrImagesResult = [];
        foreach ($arrImagesSources as $imageSource)
        {
            $imageElement = [];
            if (is_array($imageSource))
            {
                if (is_array($imageSource[1])&&(is_array($imageSource[2])))
                {
                    for ($index=0; $index < count($imageSource[1]); $index++)
                    {
                        if ($imageSource[1][$index] == 'src') array_push($imageElement,['src'=>$imageSource[2][$index]]); else
                        if ($imageSource[2][$index] == 'alt') array_push($imageElement,['alt'=>$imageSource[2][$index]]); else
                        if ($imageSource[2][$index] == 'title') array_push($imageElement,['title'=>$imageSource[2][$index]]); else
                        if ($imageSource[2][$index] == 'class') array_push($imageElement,['class'=>$imageSource[2][$index]]);
                    }
                }
            }
            if (($imageElement != []) && (isset($imageElement['src']) && (strlen($imageElement['src'])>5)))
                array_push($arrImagesResult, $imageElement);
        }

        return $arrImagesSources;
    }

    public function getFirstImageFromHTML($sText)
    {
        $arrImagesSources = $this->processHTMLForImages($sText);
        if (count($arrImagesSources) > 0)
            return $arrImagesSources[0];
    }

    public function getAllImagesFromHTML($sText)
    {
        $arrImagesSources = $this->processHTMLForImages($sText);
        return $arrImagesSources;
    }

    private function UnitTesting()
    {
        $sMessage = '<!DOCTYPE html>
            <html>
            <head>
            <style>
            img { 
                width:100%; 
            }
            </style>
            </head>
            <body>
            
            <img src="html5.gif" alt="HTML5 Icon" style="width:128px;height:128px;">
            <img src="html5.gif" alt="HTML5 Icon" width="128" height="128">
            
            </body>
            </html>';
        $this->processHTMLForImages($sMessage);

        /*$sMessage = '<p><p>awesome ;;) :) ;)) =))<p></p>';
        $this->processForPlainTextEmojis($sMessage,true);
        $this->renderEmojisImages($sMessage);
        echo $sMessage;*/

        /*
        $sMessage = '<p>cool awsome <img src="https://assets-cdn.github.com/images/icons/emoji/unicode/1f604.png?v6" emoji="enabled" style="max-height: 16px;">&nbsp;<img src="https://assets-cdn.github.com/images/icons/emoji/unicode/1f36a.png?v6" emoji="enabled" style="max-height: 16px;">&nbsp;it is cool ;) :/ awesome =))<p></p></p>';
        $this->processForPlainTextEmojis($sMessage,true);
        $this->renderEmojisImages($sMessage);
        echo $sMessage;*/
        /*
        $sMessage = '<p><p><img src="https://assets-cdn.github.com/images/icons/emoji/unicode/1f604.png?v6" emoji="enabled" style="width: 16px;">&nbsp;&nbsp;&nbsp;<img src="https://assets-cdn.github.com/images/icons/emoji/unicode/1f3e9.png?v6" emoji="enabled" style="width: 16px;">&nbsp;x&nbsp;</p></p>';
        $this->removeEmojisImages($sMessage);
        echo $sMessage;
        $this->renderEmojisImages($sMessage);
        echo $sMessage;
        */

        //$sMessage = '<p><p>#EmOjI25@:smile#$&nbsp;#EmOjI25@:love_hotel#$&nbsp;x&nbsp;</p></p>';
        //$this->renderEmojisImages($sMessage);

        /*
        $sMessage = 'awesome #EmOjI25@(:#$    cool #EmOjI25@:x#$';
        $this->renderEmojisImages($sMessage);
        echo $sMessage;
        */

        /*
         $sMessage='awesome :smile :) cool awesome xxx :D cool =))';
         $this->processForPlainTextEmojis($sMessage,true);
         echo $sMessage;
        */
    }


}