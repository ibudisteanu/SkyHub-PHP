<div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp main-breadcrumb" style="padding-top: 20px">

    <div class="row btn-breadcrumb-row">
        <ol class="btn-group btn-breadcrumb" style="list-style: none; padding-left: 0;">

            <?php
                /*example
                    <a href="#" class="btn btn-info">Snippets</a>
                    <a href="#" class="btn btn-success">Breadcrumbs</a>
                */
                echo '<li class="breadcrumb-li btn btn-primary" ><a href="'.base_url('').'" ><i class="glyphicon glyphicon-home"></i></a></li>';
                for ($index=0; $index < count($arrBreadCrumb); $index++)
                {
                    $element = $arrBreadCrumb[$index]; $sActive='';
                    //if ($index == count($arrBreadCrumb)-1) $sActive = 'class="active"';

                    //echo '<li style="all: initial;">';

                    switch ($element['domain'])
                    {
                        case "forum":
                            $buttonType="btn-danger";
                            break;
                        case "category":
                            $buttonType="btn-warning";
                            break;
                        case "forum/category":
                            $buttonType = "btn-info";
                            break;
                        case "topic":
                            $buttonType = "btn-primary";
                            break;
                        case "profile":
                            $buttonType = "btn-danger";
                            break;
                        case "page":
                            $buttonType = "btn-success";
                            break;
                        default:
                            $buttonType = "btn-info";
                            break;
                    }

                    //echo '<li '.$sActive.'>';
                    if ($element['url'] != '') echo '<li class="btn '.$buttonType.'" ><a href="'.$element['url'].'">';

                    echo $this->StringsAdvanced->processText($element['name'],'html|xss|whitespaces');

                    if ($element['url'] != '') echo '</a></li>';

                    //echo '</li>';
                }
            ?>
        </ol>
    </div>
</div>