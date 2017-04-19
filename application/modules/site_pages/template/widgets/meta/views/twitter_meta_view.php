<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@ibudisteanu" />
<meta name="twitter:title" content="<?=$MetaTitle?>" />
<meta name="twitter:description" content="<?=$MetaDescription?>" />
<?php
    if (is_string($MetaImage))
        echo '<meta name="twitter:image" content="'.$MetaImage.'" />';
    else
        if (is_array($MetaImage))
            foreach ($MetaImage as $Image)
                echo '<meta name="twitter:image" content="'.$Image['src'].'" />';
?>