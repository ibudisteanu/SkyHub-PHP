
<div class="col-xs-12" style="font-size: 1.4em; padding-bottom: 20px" >
        <a href="<?=$dtForumCategory->getFullURL()?>">
            <?php

            if (($this->StringsAdvanced->startsWith($dtForumCategory->sImage,"fa"))||($this->StringsAdvanced->startsWith($dtForumCategory->sImage,"glyphicon")))
                echo '<i class="'.$dtForumCategory->sImage.'" style="padding-right:10px"></i>';
            else
                if ($sImage != '') echo '<img src="'.$dtForumCategory->sImage.'" alt="'.$dtForumCategory->sName.'">';

            echo $dtForumCategory->sName;
            echo '</a>';

            if ($this->MyUser->bLogged)
            {
                echo '<a href="'.$dtForumCategory->getFullURL().'/add-topic#AddForumTopic'.'">';
                echo '<i class="fa fa-pencil-square-o text-too" style="padding-left:20px"></i>';
                echo '</a>';
            }

            if ($dtForumCategory->checkOwnership())
            {
                echo '<a href="'.$dtForumCategory->getFullURL().'/edit-forum-category#AddForumCategory'.'"><i class="glyphicon glyphicon-wrench text-too" style="padding-left:15px"></i></a>';
            }
            ?>
        </a>
</div>


<?php /*

<div class="col-xs-12">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-md-7" style="font-size: 1.3em;">
                    <a href="<?=$dtForumCategory->getFullURL()?>">
                        <?php

                            if (($this->StringsAdvanced->startsWith($dtForumCategory->sImage,"fa"))||($this->StringsAdvanced->startsWith($dtForumCategory->sImage,"glyphicon")))
                            echo '<i class="'.$dtForumCategory->sImage.'" style="padding-right:10px"></i>';
                            else
                            if ($sImage != '') echo '<img src="'.$dtForumCategory->sImage.'" alt="'.$dtForumCategory->sName.'">';

                            echo $dtForumCategory->sName;
                            echo '</a>';

                            if ($this->MyUser->bLogged)
                            {
                            echo '<a href="'.$dtForumCategory->getFullURL().'/add-topic#AddForumTopic'.'">';
                                echo '<i class="fa fa-pencil-square-o text-too" style="padding-left:20px"></i>';
                                echo '</a>';
                            }

                            if ($dtForumCategory->checkOwnership())
                            {
                            echo '<a href="'.$dtForumCategory->getFullURL().'/edit-forum-category#AddForumCategory'.'"><i class="glyphicon glyphicon-wrench text-too" style="padding-left:15px"></i></a>';
                            }
                        ?>
                    </a>
                </th>
                <th class="col-md-1">Comments</th>
                <th class="col-md-1">Users</th>
                <th class="col-md-1">Last Post</th>
            </tr>
            </thead>


//VERSION 2
<table class="table forum table-striped">
    <thead>
    <tr>
        <th class="cell-stat" style="width:40px"></th>
        <th>
            <h3 style="margin-left: -40px">
                <a href="<?=$dtForumCategory->getFullURL()?>">
                <?php

                    if (($this->StringsAdvanced->startsWith($dtForumCategory->sImage,"fa"))||($this->StringsAdvanced->startsWith($dtForumCategory->sImage,"glyphicon")))
                        echo '<i class="'.$dtForumCategory->sImage.'" style="padding-right:10px"></i>';
                    else
                        if ($sImage != '') echo '<img src="'.$dtForumCategory->sImage.'" alt="'.$dtForumCategory->sName.'">';

                    echo $dtForumCategory->sName;
                    echo '</a>';

                    if ($this->MyUser->bLogged)
                    {
                        echo '<a href="'.$dtForumCategory->getFullURL().'/add-topic#AddForumTopic'.'">';
                        echo '<i class="fa fa-pencil-square-o text-too" style="padding-left:20px"></i>';
                        echo '</a>';
                    }

                    if ($dtForumCategory->checkOwnership())
                    {
                        echo '<a href="'.$dtForumCategory->getFullURL().'/edit-forum-category#AddForumCategory'.'"><i class="glyphicon glyphicon-wrench text-too" style="padding-left:15px"></i></a>';
                    }

                ?>
            </h3>
        </th>
        <th class="cell-stat text-center hidden-xs hidden-sm">Comments</th>
        <th class="cell-stat text-center hidden-xs hidden-sm">Views</th>
        <th class="cell-stat-2x hidden-xs hidden-sm">Last Post</th>
    </tr>
    </thead>
    <tbody>
 */?>