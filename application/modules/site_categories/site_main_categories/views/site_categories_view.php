<div class="row" style="margin:0">
    <!-- section about start -->
    <?php

        $iColumnIndex=0;

        if (count($dtSiteCategories) == 0)
        {
            echo 'no categories in the Data Base';
            return;
        }

        foreach($dtSiteCategories as $siteCategory)
        {
            if ($iColumnIndex==0){}

            $iColumnIndex++;

            $data['siteCategory'] = $siteCategory;
            //var_dump($siteCategory).'<br/><br/><br/><br/>';
            $this->load->view('site_category_view',$data);

            //echo "Key=" . $forum_category . ", Value=" . $forum_category_value;         echo "<br>";

            if ($iColumnIndex==4)
                $iColumnIndex=0;

        }

        if ($iColumnIndex>0)
            $iColumnIndex=0;

    ?>
</div>