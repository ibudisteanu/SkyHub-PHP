<?php

//DOCUMENTATION http://ip-api.com/json/208.80.152.201

class Ip extends MY_Model
{
    public $sCountry;
    public $sCountryCode;
    public $sRegionName;
    public $sCity;
    public $sTimeZone;
    public $dbLocationLat;
    public $dbLocationLong;
    public $sIP;

    protected function getRealUserIp(){
        switch(true){
            case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
            case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
            case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
            default : return $_SERVER['REMOTE_ADDR'];
        }
    }

    function __construct()
    {
        parent::__construct();
        $this->load->library('input');

        $this->sTimeZone='Europe/London';

        $this->sIP = $this->getRealUserIp();
        if ($this->sIP=='127.0.0.1')
            $this->sIP = '86.127.0.1';

        $line = date('Y-m-d H:i:s') . " - $this->sIP";
        file_put_contents('visitors.log', $line . PHP_EOL, FILE_APPEND);

/*        if ($this->sIP == '188.24.226.255')
        {
            die();
        }*/

        $this->getIPGeoLocation();
        //init();
    }

    protected function getIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->sIP = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->sIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->sIP = $_SERVER['REMOTE_ADDR'];
        }
    }

    public function init()
    {
        $this->sCountry='United States';
        $this->sCountryCode='us';
        $this->sTimeZone = 'Europe/London';
        $this->sCity='New York';
    }

    function curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); //timeout in seconds
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    protected function getIPGeoLocation()
    {
        $this->getIPGeoLocationService2();
    }

    protected function getIPGeoLocationService1()
    {
        try
        {
            $sCacheId = 'getIPGeoLocationService1_'.$this->sIP;
            if (!$file = $this->AdvancedCache->get($sCacheId )) {
                $file = $this->curl("http://ip-api.com/json/" . $this->sIP);
                if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $file, 2678400);
            }

            $json = json_decode($file); //echo json_encode($json);

            if ((isset($json->status))&&($json->status == 'success'))
            {
                $this->sCountry=$json->country;
                $this->sCity=$json->city;
                $this->sCountryCode=$json->countryCode;
                //echo 'IP:'.$this->sCountryCode;
                $this->sTimeZone=$json->timezone;
                $this->sRegionName=$json->regionName;
                $this->dbLocationLat=$json->lat;
                $this->dbLocationLong=$json->lon;

            } else $this->init();

        } catch (Exception $e)
        {
            $this->init();
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    protected function getIPGeoLocationService2()
    {
        try
        {
            $sCacheId = 'getIPGeoLocationService2_'.$this->sIP;
            if (!$file = $this->AdvancedCache->get($sCacheId )) {
                $file = $this->curl("http://freegeoip.net/json/" . $this->sIP);
                if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $file, 2678400);
            }

            $json = json_decode($file); //echo json_encode($json);

            if (isset($json->ip))
            {
                $this->sCountry=$json->country_name;
                $this->sCity=$json->city;
                $this->sCountryCode=$json->country_code;
                //echo 'IP:'.$this->sCountryCode;
                $this->sTimeZone=$json->time_zone;
                $this->sRegionName=$json->region_name;
                $this->dbLocationLat=$json->latitude;
                $this->dbLocationLong=$json->longitude;

            } else $this->init();

        } catch (Exception $e)
        {
            $this->init();
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

}