<div id="page-nav">
    <div class="pagination-container">
        <ul class="pagination">
            <li class="PagedList-skipToNext"> <a href="<?=rtrim($href,'/').'/'.($index+1)?>" rel="next">Next</a></li>
            <?= ($index > 0) ? '<li class="PagedList-skipToNext"> <a href="'.rtrim($href,'/').'/'.($index).'" rel="curent">Current</a></li>' : '' ?>
            <?= ($index > 1) ? '<li class="PagedList-skipToNext"> <a href="'.rtrim($href,'/').'/'.($index-1).'" rel="previous">Previous</a></li>' : '' ?>
        </ul>
    </div>
</div>