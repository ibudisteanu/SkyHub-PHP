<div class="box box-primary">
    <div class="box-header">
        <i class="fa fa-list-alt"></i>

        <h3 class="box-title">Admin <strong>Database</strong> Functions</h3>

        <div class="box-tools pull-right">
            <ul class="pagination pagination-sm inline">
                <li><a href="#">&laquo;</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">&raquo;</a></li>
            </ul>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <p>
            <a href="<?=base_url("admin/apps/createTables")?>"><button type="button" class="btn bg-maroon btn-flat margin">Create Tables</button></a>

        </p> <br/>

        <p>
            <a href="<?=base_url("admin/apps/refreshUsers")?>"<button type="button" class="btn bg-purple btn-flat margin">Refresh Users</button></a>
            <a href="<?=base_url("admin/apps/refreshForumsByReSaving")?>"<button type="button" class="btn bg-red btn-flat margin">Refresh Forums By ReSaving</button></a>
            <a href="<?=base_url("admin/apps/refreshForumCategoriesByReSaving")?>"<button type="button" class="btn bg-red btn-flat margin">Refresh Forum Categories By ReSaving</button></a>
            <a href="<?=base_url("admin/apps/refreshSiteCategoriesByReSaving")?>"<button type="button" class="btn bg-red btn-flat margin">Refresh Site Categories By ReSaving</button></a>
            <a href="<?=base_url("admin/apps/refreshTopicsComponentReplies")?>"<button type="button" class="btn bg-green btn-flat margin">Refresh Topics Component Replies</button></a>
            <a href="<?=base_url("admin/apps/refreshTopicsByReSaving")?>"<button type="button" class="btn bg-green btn-flat margin">Refresh Topics By ReSaving</button></a>

            <a href="<?=base_url("admin/apps/refreshMaterializedChildrenSiteCategories")?>"<button type="button" class="btn bg-green btn-flat margin">Refresh All the Elements Materialized Site Categories by Resaving</button></a>

        </p> <br/>


        <p>
            <a href="<?=base_url("admin/apps/sortingCoefficientsRefresh")?>"><button type="button" class="btn bg-maroon btn-flat margin">Refresh Sorting Coefficients</button></a>
            <a href="<?=base_url("admin/apps/sortingCoefficientsDelete")?>"<button type="button" class="btn bg-purple btn-flat margin">Delete Sorting Coefficients</button></a>
        </p>

        <p>
            <?=$content?>
        </p>

    </div>
    <!-- /.box-body -->

</div>
