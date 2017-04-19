
<ul class="col-xs-12 tags-container tags keywords">
    <b>Tags: </b>
        <?php
            foreach ($arrTags as $Tag)
                if (strlen($Tag) > 1)
                {
                    echo '<li class="tag-element"><button type="button" class="btn btn-basic" >'.$Tag.'</button></li>';
                }
        ?>
</ul>