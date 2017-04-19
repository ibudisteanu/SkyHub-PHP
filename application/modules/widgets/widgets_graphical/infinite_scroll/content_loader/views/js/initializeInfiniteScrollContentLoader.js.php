 $(document).ready(function() {
     infiniteScrollConstructor("<?=$sInfiniteScrollContainerName?>",<?=$bEnableInfiniteScroll?>,<?=$iPageIndex?>,<?=$iPageElementsCount?>,"<?=$sParentId?>",<?=$bHasNext?>,<?=json_encode($arrInfiniteScrollDisplayContentType)?>,"<?=$sInfiniteScrollActionName?>");
 });
