<?php

class StringEmojisProcessing
{
    public $arrEmojisDots;
    public $arrEmojisAll;

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

    /*
     * <p> <p>
     *      <img src="https://assets-cdn.github.com/images/icons/emoji/unicode/1f604.png?v6" style="width: 16px;">
     *      <img src="https://assets-cdn.github.com/images/icons/emoji/unicode/1f604.png?v6" style="width: 16px;">&nbsp;</p></p>
     */
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

    private function processTextEmojis(&$sText, $arrEmojis, $sEmojiPrefix='', $bEmojiDBCode=false)
    {
        foreach ($arrEmojis as $EmojiItemCode => $EmojiItemUrl) {

            $posEmoji = strpos($sText, $sEmojiPrefix.$EmojiItemCode);

            while ($posEmoji > 0)
            {
                $sEmojiCode = $sEmojiPrefix.$EmojiItemCode; $sNextChar='';
                if ($posEmoji + strlen($sEmojiCode)  < strlen($sText) ) $sNextChar = $sText[$posEmoji + strlen($sEmojiCode) ];

                if (($sEmojiPrefix==':')&&($sNextChar == ':'))
                {
                    $sEmojiCode .= ':';
                }

                if ($posEmoji + strlen($sEmojiCode)  < strlen($sText) ) $sNextChar = $sText[$posEmoji + strlen($sEmojiCode) ];

                $sPreviousChar = $sText[$posEmoji-1];

                //echo '@'.$sEmojiCode.' previous='.$sPreviousChar.' next='.$sNextChar.'@';

                if (($sNextChar=='\\')||($sNextChar=='/')||($sPreviousChar=='@')||($sNextChar=='#'))
                {
                    $posEmoji = strpos($sText, $sEmojiPrefix . $EmojiItemCode, $posEmoji+1);
                } else {
                    $str1 = substr($sText, 0, $posEmoji);
                    $str2 = substr($sText, $posEmoji + strlen($sEmojiCode), strlen($sText));
                    //echo $str1.'###'.$str2.'       ';
                    if ($bEmojiDBCode == false)
                        $strEmojiImage = '<img src="' . $EmojiItemUrl . '" emoji="enabled" style="max-height:16px;">';
                    else
                        $strEmojiImage = '#EmOjI25@' . $sEmojiCode . '#$';

                    $sText = $str1 . $strEmojiImage . $str2;

                    $posEmoji = strpos($sText, $sEmojiPrefix . $EmojiItemCode, strlen($str1 . $strEmojiImage));
                }
            }
        }
    }

    public function processForPlainTextEmojis(&$sText,$bEmojiDBCode=false)
    {
        $this->processTextEmojis($sText, $this->arrEmojisDots,':',$bEmojiDBCode);
        $this->processTextEmojis($sText, $this->arrEmojisAll,'',$bEmojiDBCode);
    }

    private function UnitTesting()
    {
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