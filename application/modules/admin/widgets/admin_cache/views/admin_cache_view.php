<div class="box box-primary">
    <div class="box-header">
        <i class="fa fa-list-alt"></i>

        <h3 class="box-title">Admin Cache</h3>

        <div class="box-tools pull-right">
            <ul class="pagination pagination-sm inline">
                <li><a href="#">&laquo;</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">&raquo;</a></li>
            </ul>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <div class="col-md-12">
            <a href="<?=base_url("admin/apps/clear-cache")?>"><button type="button" class="btn bg-maroon btn-flat margin"><i class="fa fa-cubes"></i> Clear Cache</button></a>
        </div>

        <form id="loginform"  class="form-horizontal" action="<?= base_url('admin/apps/clean-cache');?>" role="form" method="post">
            <div class="col-md-8">
                <div class="form-group">
                    <select class="form-control" name="cleanCacheActionName">

                        <?php
                            $arr = ['findCategories','findForumCategoriesByForumId','findForumsFromSiteCategory','findTopCategories','readAllUserActivities','userByMongoId'];
                            foreach ($arr as $element)
                            {
                                echo '<option ';
                                if ((isset($_POST['cleanCacheActionName']))&&($_POST['cleanCacheActionName'] == $element)) echo 'selected';
                                echo '>'.$element.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fa fa-cube"></i> Clean Cache</button>
            </div>

            <div class="col-md-12">
                <?= isset($sText) ? $sText : '' ?>
            </div>

        </form>


    </div>
    <!-- /.box-body -->

</div>
