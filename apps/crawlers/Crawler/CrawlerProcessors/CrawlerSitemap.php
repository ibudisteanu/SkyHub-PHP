<?php

require_once __DIR__.'/../CrawlerProcessor.php';
require_once __DIR__ . '/../../../helpers/skyhub_server/server_uri.php';

class CrawlerSitemap extends CrawlerProcessor
{
    //public  $sFileName = __DIR__.'/../xmls/sitemap.xml';
    public  $sFileName = __DIR__ . '/../../../../uploads/sitemap/sitemap.xml';
    public  $myfile;

    public function __construct()
    {
        parent::__construct();

        $this->createXMLFile();
        echo 'Creating Crawler Instance ... <br/>';
    }

    function xml_entities($string) {
        return strtr(
            $string,
            array(
                "<" => "&lt;",
                ">" => "&gt;",
                '"' => "&quot;",
                "'" => "&apos;",
                "&" => "&amp;",
            )
        );
    }

    public function createXMLFile()
    {
        $this->myfile = fopen($this->sFileName, "w") or die("Unable to open file!");

        fwrite($this->myfile, '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
>
  <url>
    <loc>http://skyhub.me/</loc>
    <lastmod>'.date('c').'</lastmod>
    <changefreq>hourly</changefreq>
    <priority>1</priority>
  </url>'."\n");

    }

    public  function closeXMLFile()
    {
        fwrite($this->myfile,'</urlset>');
        fclose($this->myfile);
    }

    protected function processNewsDOM($CrawlerDataInformation, $url, $DOM)
    {
//        fwrite($this->myfile,'    <loc>'.urlencode($url).'</loc>
        fwrite($this->myfile,'  <url><loc>'.$this->xml_entities($url).'</loc>
    <lastmod>'.date('c').'</lastmod>
    <changefreq>hourly</changefreq>
    <priority>'.(string)(rand(8,10)/10).'</priority></url>'."\n");

        //fflush($this->myfile);

        echo $url;
        //flush();
    }

    protected  function jobFinished()
    {
        $this->closeXMLFile();
    }
}