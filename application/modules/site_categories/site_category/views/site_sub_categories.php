<?php
    $iColumnIndex=0;

    //echo $dtSiteCategory->calculateFullURL();

    if (((count($dtSiteSubCategories) == 0)||(count($dtSiteSubCategories) == 1)&&($dtSiteSubCategories[0]==null)))
    {
        //echo '<h3 style="text-align: center">No Site Sub Categories</h3>';
    } else
    foreach($dtSiteSubCategories as $forumCategory)
    if ($forumCategory != null)
    {
        if ($iColumnIndex==0)
        {
            echo '<div class="row sub-categories-row">';
            echo '<div class="feature-wrapper text-center">';
        }
        $iColumnIndex++;

        $data['siteCategory'] = $forumCategory;
        $this->load->view('site_sub_category.php',$data);

        //echo "Key=" . $forum_category . ", Value=" . $forum_category_value;
        //echo "<br>";

        if ($iColumnIndex==8)
        {
            echo '</div><!-- feature-wrapper end -->';
            echo '</div><!-- row end -->';
            $iColumnIndex=0;
        }

    }

    if ($iColumnIndex>0)
    {
        echo'    </div><!-- feature-wrapper end -->
                         </div><!-- row end -->';
        $iColumnIndex=0;
    }

