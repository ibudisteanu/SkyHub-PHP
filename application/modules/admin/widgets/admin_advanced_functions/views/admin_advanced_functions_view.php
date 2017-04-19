<div class="box box-primary">
    <div class="box-header">
        <i class="fa fa-list-alt"></i>

        <h3 class="box-title">Admin Advanced Functions</h3>

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
        <a href="<?=base_url("apps/crawlers/crawler_sitemap_index.php")?>"><button type="button" class="btn bg-maroon btn-flat margin"><i class="fa fa-sitemap"></i> Generate Site Map</button></a>
        <a href="<?=base_url("apps/crawlers/crawler_index.php")?>"<button type="button" class="btn bg-purple btn-flat margin"><i class="fa fa-newspaper-o"></i> Crawl News</button></a>
        <a href="<?=base_url("api/crawler/news/categories")?>"<button type="button" class="btn bg-navy btn-flat margin"><i class="fa fa-pencil-square-o"></i> Generate JSON News Categories</button></a>
    </div>
    <!-- /.box-body -->

</div>
