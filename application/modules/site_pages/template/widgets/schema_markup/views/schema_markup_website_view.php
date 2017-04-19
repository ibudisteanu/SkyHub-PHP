//NOT USED ANYMORE

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "<?=base_url("")?>",
  "name": <?=json_encode(WEBSITE_TITLE)?>,
   "author": {
      "@type": "Person",
      "name": "Ionut Alexandru Budisteanu"
    },
  "description": <?=json_encode(WEBSITE_META_DESCRIPTION)?>,
  "publisher": "BIT TECHNOLOGIES RO",
  "potentialAction": {
    "@type": "SearchAction",
    "target": <?=json_encode(base_url("s/{search_term}"))?>,
    "query-input": "required name=search_term" }
    }
</script>


//array_push($this->arrSchemas,$this->load->view('../modules/site/template/schema_markup/views/schema_markup_website_view.php', null, true));