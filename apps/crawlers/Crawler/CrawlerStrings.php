<?php

class CrawlerStrings{

    public $CrawlerInfo;

    public function __construct()
    {
    }

    protected function verifyIllegalChars($cChar, $arrIllegalChars=[' ','�',"\xA0","\xC2","\r\n", '\\n', '\\r', "\n", "\r", "\t", "\0", "\x0B"])
    {
        for ($index=0; $index < count($arrIllegalChars); $index++) {
            if ($arrIllegalChars[$index] == $cChar) {
                return true;
            }
        }
        return false;
    }

    public function fixDateTime(&$sText)
    {
        $sText = str_replace('/','.',$sText);
        $sText = str_replace('-','.',$sText);
        $sText = strtolower($sText);
    }

    public function replaceDates(&$sText)
    {
        $sText = strtolower($sText);
        $sText = str_replace("ianuarie","Jan",$sText); $sText = str_replace("ian","Jan",$sText);
        $sText = str_replace("februarie","Feb",$sText);
        $sText = str_replace("martie","Mar",$sText);
        $sText = str_replace("aprilie","Apr",$sText);
        $sText = str_replace("mai","May",$sText);
        $sText = str_replace("iunie","Jun",$sText); $sText = str_replace("iun","Jun",$sText);
        $sText = str_replace("iulie","Jul",$sText); $sText = str_replace("iul","Jul",$sText);
        $sText = str_replace("septembrie","Sep",$sText);
        $sText = str_replace("octombrie","Oct",$sText);
        $sText = str_replace("noi","Nov",$sText); $sText = str_replace("noiembrie","Nov",$sText);
        $sText = str_replace("decembrie","Dec",$sText);
    }

    public function repairURL(&$sURL, $sWebsiteURL = '')
    {
        if ($sWebsiteURL == '') $sWebsiteURL = $this->CrawlerInfo->sWebsite;

        $urlData = parse_url($sURL);
        if (isset($urlData['scheme']) == false)
        {
            if (isset($urlData['host']) == false)
                $sURL = rtrim($sWebsiteURL,'/').'/'. ltrim($sURL,'/');
            else
                $sURL = rtrim('http://') . ltrim($sURL,'/');
        }

        $urlData = parse_url($sURL);
        if (!(($urlData['scheme'] == 'http') || ($urlData['scheme'] == 'https')))
        {
            if (isset($urlData['host']) == false)
                $sURL = rtrim($this->CrawlerInfo->sWebsite,'/').'/'. ltrim($sURL,'/');
            else
                $sURL = rtrim('http://') . ltrim($sURL,'/');
        }
    }



    public function removeWhiteSpace($sText)
    {
        if (is_array($sText)) return;
        $beginIndex=0; $endIndex = strlen($sText)-1;
        while (($beginIndex < strlen($sText)) && ($this->verifyIllegalChars($sText[$beginIndex]))) $beginIndex++;
        while (($endIndex > 0) && ($this->verifyIllegalChars($sText[$endIndex]))) $endIndex--;
        $sTextNew='';
        for ($index=$beginIndex; $index <= $endIndex; $index++)
        {
            $sTextNew .= $sText[$index];
        }
        return $sTextNew;
    }

    public function processText($sText, $sFiltration='html|xss|whitespaces')
    {
        $arrFilters = explode("|",$sFiltration);
        foreach ($arrFilters as $filter)
            if ($filter == 'html') $sText = strip_tags($sText);
            else
                if ($filter == 'xss') $sText = $this->xss_clean($sText);
                else
                    if ($filter == 'whitespaces') $sText = $this->removeWhiteSpace($sText);
        $sText = $this->closeTags($sText);
        return $sText;
    }

    public function processOnlyText($sURLName, $sFiltration='html|xss|whitespaces')
    {
        if ($sFiltration == '') $sFiltration = 'html|xss|whitespaces';
        $sURLName=$this->processText($sURLName, $sFiltration);
        $sText = $this->urlTitleForeign($sURLName);
        return $sText;
    }

    function urlTitleForeign($str, $separator = 'dash', $lowercase = FALSE)
    {
        $foreign_characters = array(
            '/ä|æ|ǽ/' => 'ae',
            '/ö|œ/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
            '/Б/' => 'B',
            '/б/' => 'b',
            '/Ç|Ć|Ĉ|Ċ|Č|Ц/' => 'C',
            '/ç|ć|ĉ|ċ|č|ц/' => 'c',
            '/Ð|Ď|Đ|Д/' => 'D',
            '/ð|ď|đ|д/' => 'd',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Ё|Э/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě|е|ё|э/' => 'e',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
            '/Ф/' => 'F',
            '/ф/' => 'f',
            '/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
            '/ĝ|ğ|ġ|ģ|г/' => 'g',
            '/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
            '/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
            '/Ĥ|Ħ|Х/' => 'H',
            '/ĥ|ħ|х/' => 'h',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
            '/Ĵ|Й/' => 'J',
            '/ĵ|й/' => 'j',
            '/Ķ|К/' => 'K',
            '/ķ|к/' => 'k',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
            '/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
            '/М/' => 'M',
            '/м/' => 'm',
            '/Ñ|Ń|Ņ|Ň|Н/' => 'N',
            '/ñ|ń|ņ|ň|ŉ|н/' => 'n',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
            '/П/' => 'P',
            '/п/' => 'p',
            '/Ŕ|Ŗ|Ř|Р/' => 'R',
            '/ŕ|ŗ|ř|р/' => 'r',
            '/Ś|Ŝ|Ş|Š|С/' => 'S',
            '/ś|ŝ|ş|š|ſ|с/' => 's',
            '/Ţ|Ť|Ŧ|Т/' => 'T',
            '/ţ|ť|ŧ|т/' => 't',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
            '/В/' => 'V',
            '/в/' => 'v',
            '/Ý|Ÿ|Ŷ|Ы/' => 'Y',
            '/ý|ÿ|ŷ|ы/' => 'y',
            '/Ŵ/' => 'W',
            '/ŵ/' => 'w',
            '/Ź|Ż|Ž|З/' => 'Z',
            '/ź|ż|ž|з/' => 'z',
            '/Æ|Ǽ/' => 'AE',
            '/ß/'=> 'ss',
            '/Ĳ/' => 'IJ',
            '/ĳ/' => 'ij',
            '/Œ/' => 'OE',
            '/ƒ/' => 'f',
            '/Ч/' => 'Ch',
            '/ч/' => 'ch',
            '/Ю/' => 'Ju',
            '/ю/' => 'ju',
            '/Я/' => 'Ja',
            '/я/' => 'ja',
            '/Ш/' => 'Sh',
            '/ш/' => 'sh',
            '/Щ/' => 'Shch',
            '/щ/' => 'shch',
            '/Ж/' => 'Zh',
            '/ж/' => 'zh',
            '/Х/' => 'Kh',
            '/х/' => 'kh',
            '/Ц/' => 'Ts',
            '/ц/' => 'ts',
            '/Ъ|ъ|Ь|ь/' => '',
            '/ξ/' => 'ks',
            '/π/' => 'p',
            '/β/' => 'v',
            '/μ/' => 'm',
            '/ψ/' => 'ps',
            '/Ё/' => 'Yo',
            '/ё/' => 'yo',
            '/Є/' => 'Ye',
            '/є/' => 'ye',
            '/Ї/' => 'Yi',
            '/Д/' => 'D',
            '/д/' => 'd',
            '/Ð|Ď|Đ|Δ/' => 'Dj',
            '/ð|ď|đ|δ/' => 'dj',
            '/Ĵ/' => 'J',
            '/ĵ/' => 'j',
            '/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
            '/ŕ|ŗ|ř|ρ|р/' => 'r',
            '/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
            '/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
            '/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
            '/ț|ţ|ť|ŧ|т/' => 't',
            '/Þ|þ/' => 'th',
            '/”/' => '"'
        );
        $str = preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $str);
        //$replace = ($separator == 'dash') ? '-' : '_';
        $replace = ($separator == 'dash') ? ' ' : ' ';

        if ($lowercase === TRUE)
        {
            if( function_exists('mb_convert_case') )
            {
                $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
            }
            else
            {
                $str = strtolower($str);
            }
        }
        $str = preg_replace('#[^a-z 0-9~%.: _\-]#i', '', $str);
        return trim(stripslashes($str));
    }

    public function closeTags($html) {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i=0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }
    function xss_clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);
        // we are done...
        return $data;
    }

    public function getDateRegexString($sText)
    {
        $sText = strtolower($sText);
        /*var_dump('/('.$this->getMonthsForRegex().'?)(\s?)+(,|.|:?)(\s?)+\d{1,2}(\s?)+(,|.|:?)(\s?)+\d{4}/');
        var_dump('/\d{1,2}(\s?)+(,|.|:?)(\s?)+('.$this->getMonthsForRegex().'?)\s+(,|.|:?)\s?+\d{4}/');*/

        //EX 12 apr 2013 sau 12:apr:2013
        if (preg_match('/('.$this->getMonthsForRegex().'?)(\s?)+(,|.|:?)(\s?)+\d{1,2}(\s?)+(,|.|:?)(\s?)+\d{4}/', $sText, $matches))
            return $matches[0];

        //apr 12 2013 sau apr:12:2013
        if (preg_match('/\d{1,2}(\s?)+(,|.|:?)(\s?)+('.$this->getMonthsForRegex().'?)\s+(,|.|:?)\s?+\d{4}/', $sText, $matches))
            return $matches[0];

        if (preg_match('/(?:(?:(?:0?[13578]|1[02])(\/|-|\.)31)\1|(?:(?:0?[1,3-9]|1[0-2])(\/|-|\.)(?:29|30)\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:0?2(\/|-|\.)29\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:(?:0?[1-9])|(?:1[0-2]))(\/|-|\.)(?:0?[1-9]|1\d|2[0-8])\4(?:(?:1[6-9]|[2-9]\d)?\d{2})/',$sText, $matches))
            return $matches[0];

        //Example 13 :   03 : 2020 SAU 13:03:20
        if (preg_match('/([0-9]{2})(\s?)+(.|:|,)(\s?)+([0-9]{2})(\s?)+(.|:|,)(\s?)+((([0-9]{4})|([0-9]{2})))/', $sText, $matches))
            return $matches[0];

        return '';
    }

    protected $arrMonths = [
        ['name'=>['jan','january','ian','ianuarie'], 'value'=>'1'],
        ['name'=>['feb','february','februarie'], 'value'=>'2'],
        ['name'=>['mar','march','mar','martie'],'value'=>'3'],
        ['name'=>['apr','april','apr','aprilie'],'value'=>'4'],
        ['name'=>['may','mai','m'], 'value'=>'5'],
        ['name'=>['jun','june','iun','iunie'], 'value'=>'6'],
        ['name'=>['jul','july','iul','iulie'], 'value'=>'7'],
        ['name'=>['aug','august','aug','iulie'], 'value'=>'8'],
        ['name'=>['sep','sept','september','septembrie'] , 'value'=>'9'],
        ['name'=>['oct','o','october','octombrie'] , 'value'=>'10'],
        ['name'=>['nov','november','noi','noiembrie'] , 'value'=>'11'],
        ['name'=>['dec','december','decembrie'] , 'value'=>'12'],
    ];

    public function getMonthsForRegex()
    {
        //(Jan(uary)?|Feb(ruary)?|Mar(ch)?|Apr(il)?|May|Jun(e)?|Jul(y)?|Aug(ust)?|Sep(tember)?|Oct(ober)?|Nov(ember)?|Dec(ember)?)\s+\d{1,2}(,|.|)\s+\d{4}
        $sRegex = '';
        foreach ($this->arrMonths as $month)
            foreach ($month as $key => $value)
                if ($key == 'name')
                    foreach ($value as $monthName)
                        $sRegex = $sRegex . $monthName.'|';

        $sRegex = rtrim($sRegex,'|');
        return $sRegex;
    }

    public function convertMonthStringToNumber($sMonthName)
    {
        foreach ($this->arrMonths as $month)
            foreach ($month as $key => $value) {
                if ($key == 'name')
                    foreach ($value as $monthName)
                        if ($monthName == $sMonthName)
                            $bFound = true;

                if ($key == 'value')
                    if ($bFound)
                        return $value;
            }

        return 0;
    }

    public function testDateString()
    {
        var_dump($this->getDateRegexString('de g.s.           25 sep 2016   12:45'));
        var_dump($this->getDateRegexString('05/03/2012'));
        var_dump($this->getDateRegexString('02.03.2012'));
    }

    public function getTimeRegexString($sText)
    {
        if (preg_match('/(0[1-9]:[0-5][0-9]((\ ){0,1})((AM)|(PM)|(am)|(pm)))|([1-9]:[0-5][0-9]((\ ){0,1})((AM)|(PM)|(am)|(pm)))|(1[0-2]:[0-5][0-9]((\ ){0,1})((AM)|(PM)|(am)|(pm)))/',$sText, $matches))
            return strtolower($matches[0]);

        if (preg_match('/(([0-1][0-9]|2[0-3])|([0-9]))(\.|:|-|,)([0-5][0-9])/',$sText, $matches)) //5:32 sau 5.30
            return strtolower($matches[0]);

        if (preg_match('/([0-1][0-9]|2[0-3])(\.|:|-|,)(([0-5][0-9])|[0-9])/',$sText, $matches)) //05:03 sau 05:1
            return strtolower($matches[0]);

        if (preg_match('/([0-1][0-9]|2[0-3])(\.|:|-|,)([0-5][0-9])(\.|:|-|,)([0-5][0-9])/',$sText, $matches)) //05:03:02
            return strtolower($matches[0]);

        return '';
    }

    public function testTimeString()
    {
        var_dump($this->getTimeRegexString('de g.s.           25 sep 2016   12:45'));
        var_dump($this->getTimeRegexString('de g.s.           25 sep 2016   3:45'));
        var_dump($this->getTimeRegexString('de g.s.           25 sep 2016   12:45:22'));
        var_dump($this->getTimeRegexString('de g.s.           25 sep 2016   12:45 am'));
        var_dump($this->getTimeRegexString('de g.s.           25 sep 2016   12:45am'));
        var_dump($this->getTimeRegexString('de g.s.           25 sep 2016   12:45AM'));
        var_dump($this->getTimeRegexString('de g.s.           25 sep 2016   12:45pm'));
    }
}