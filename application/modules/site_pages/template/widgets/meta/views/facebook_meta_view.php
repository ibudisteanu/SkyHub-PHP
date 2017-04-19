<meta property="fb:app_id" content="<?=$MetaFacebookAPIId?>" />
<meta property="og:type"   content="<?=$MetaPageType?>" />
<meta property="og:url"    content="<?=$MetaURL?>" />
<meta property="og:title"  content="<?=$MetaTitle?>" />
<meta property="og:description"  content="<?=$MetaDescription?>" />
<?php
    if (is_string($MetaImage))
        echo '<meta property="og:image" content="'.$MetaImage.'" />';
    else
        if (is_array($MetaImage))
            foreach ($MetaImage as $Image)
                echo '<meta property="og:image" content="'.$Image['src'].'" />';
?>