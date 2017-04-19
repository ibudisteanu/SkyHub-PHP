//NOT USED ANYMORE

<script type="application/ld+json">
{ "@context": "http://schema.org",
 "@type": "Article",
   "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": <?=json_encode($sURL)?>
  },
 "headline": <?=json_encode($sTitle)?>,
 "image": {
     "@type": "ImageObject",
     "url": <?=json_encode($sImage)?>,
     "height": 600,
     "width": 700
 },
 "alternativeHeadline": <?=json_encode(substr($sDescription, 140))?>,
 "author": <?=json_encode($sAuthor)?>,
 "editor": <?=json_encode($sEditor)?>,
 "genre": "search engine optimization",
 "keywords": <?=json_encode($sKeywords)?>,
 "wordcount": <?=json_encode(str_word_count($sDescription))?>,
 "publisher": {
    "@type": "Organization",
    "name": <?=json_encode($sPublisher)?>,
    "logo": {
      "@type": "ImageObject",
      "url": <?=json_encode(base_url("theme/images/SkyHub-logo.png"))?>
    }
  },
 "url": <?=json_encode($sURL)?>,
 "datePublished": <?=json_encode($sDatePublished)?>,
 "dateCreated": <?=json_encode($sDateCreated)?>,
 "dateModified": <?=json_encode($sDateModified)?>,
 "description": <?=json_encode($sDescription)?>,
 "articleBody": <?=json_encode($sArticle)?>
 }
</script>



//
//        $data['sTitle']=$sTitle;
//        $data['sArticle']=$sArticle;
//        $data['sDescription']=$sDescription;
//        $data['sEditor']=$sEditor;
//        $data['sImage']=$sImage;
//        $data['sURL']=$sURL;
//        $data['sAuthor']=$sAuthor;
//        $data['sKeywords']=$sKeywords;
//        $data['sDateCreated']=$sDateCreated;
//        $data['sDateModified']=$sDateModified;
//        $data['sPublisher']=$sPublisher;
//        $data['sDatePublished']=$sDatePublished;
//
//        array_push($this->arrSchemas,$this->load->view('../modules/site/template/schema_markup/views/schema_markup_article_view.php', $data, true));