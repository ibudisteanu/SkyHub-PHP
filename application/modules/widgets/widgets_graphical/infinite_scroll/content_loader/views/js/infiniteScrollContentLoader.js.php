function infiniteScrollConstructor(sInfiniteScrollContainerName, bEnableInfiniteScroll, iPageIndex, iPageElementsCount, sParentId, bHasNext, arrInfiniteScrollDisplayContentType, sInfiniteScrollActionName)
{
    var settings = [];

    if (!bEnableInfiniteScroll) bHasNext = false;

    settings['bAlreadyLoading'] = false;
    settings['iPageIndex'] = iPageIndex;
    settings['sParentId'] = sParentId;
    settings['bHaveNext'] = bHasNext;
    settings['iPageElementsCount'] = iPageElementsCount;
    settings['sInfiniteScrollActionName'] = sInfiniteScrollActionName;
    settings['arrInfiniteScrollDisplayContentType'] = arrInfiniteScrollDisplayContentType;

    settings['arrIDsAlreadyUsed'] = [];

    window['infiniteScroll'+sInfiniteScrollContainerName] = settings;

    initializeInfiniteScroll(sInfiniteScrollContainerName);
}

function initializeInfiniteScroll(sInfiniteScrollContainerName)
{
    var win = $(window);

    askForContent(sInfiniteScrollContainerName);

    // Each time the user scrolls
    win.scroll(function() {
        // End of the document reached?

        var iTop = $('#infiniteScrollLoadingPosition_'+sInfiniteScrollContainerName).offset().top - $(window).height(); //get the offset top of the element
        var iDiff = iTop - $(window).scrollTop();

        if (iDiff % 7 == 0)
            console.log(iDiff); //position of the ele w.r.t window

        if ((iDiff < 2400) && (window['infiniteScroll'+sInfiniteScrollContainerName]['bAlreadyLoading']==false)&&(window['infiniteScroll'+sInfiniteScrollContainerName]['bHaveNext'] == true))
        {
            askForContent(sInfiniteScrollContainerName);
        }
    });
}

function askForContent(sInfiniteScrollContainerName)
{
    $('#infiniteScrollLoadingRefreshSpin_'+sInfiniteScrollContainerName).show();

    console.log('Loading content for '+sInfiniteScrollContainerName);

    window['infiniteScroll'+sInfiniteScrollContainerName]['bAlreadyLoading']=true;

    var settings = window['infiniteScroll'+sInfiniteScrollContainerName];

    // AJAX request
    $.ajax({
        type: 'POST',
        url: document.location.origin+'/api/content/post/'+settings['sInfiniteScrollActionName']+'/'+settings['sParentId']+'/'+settings['iPageIndex']+'/'+settings['iPageElementsCount'],
        data: {
            'arrIDsAlreadyUsed' : settings['arrIDsAlreadyUsed'],
            'arrInfiniteScrollDisplayContentType' : settings['arrInfiniteScrollDisplayContentType']
        },
        success: function(jsonData) {

            var data;
            try {
                data = $.parseJSON(jsonData);
            }
            catch(err) {
                $('#infiniteScrollDisplayContent_'+sInfiniteScrollContainerName).append('<div class="alert alert-danger"> <strong>Error</strong> loading data (parsing the JSON) <br/> '+err+' </div>');
            }
            if (data.result)
            {

                window['infiniteScroll'+sInfiniteScrollContainerName]['bAlreadyLoading']=false;

                var $objDisplayContent = $('#infiniteScrollDisplayContent_'+sInfiniteScrollContainerName);
                console.log('#infiniteScrollDisplayContent_'+sInfiniteScrollContainerName);

                //console.log(data.content);
                window['infiniteScroll'+sInfiniteScrollContainerName]['iPageIndex']++;

                console.log(data);
                if ((data.hasOwnProperty('enableMasonry')) && (data.enableMasonry==true))
                {
                    var $elements= $(data.content);
                    var $objNew = $objDisplayContent.append( $elements);

                    $objNew.masonry( 'appended', $elements);
                    $objNew.masonry('layout');
                    $objDisplayContent.masonry('layout');

                    $objDisplayContent.imagesLoaded( function() {
                        $objDisplayContent.masonry('layout');
                    });

                    // layout Masonry after each image loads
                    $objDisplayContent.imagesLoaded().progress( function() {
                        $objDisplayContent.masonry('layout');
                    })
                } else {
                    var $elements = $(data.content);
                    $objDisplayContent.append($elements);
                    console.log('done');
                }

                if (data.hasOwnProperty('arrIDsAlreadyUsedNew'))
                    window['infiniteScroll'+sInfiniteScrollContainerName]['arrIDsAlreadyUsed'] = data.arrIDsAlreadyUsedNew;

                if ((data.hasOwnProperty('voteActivation')))
                {
                    //console.log(data.voteActivation); console.log('meeerge_vote');

                    new Function(data.voteActivation)();
                }

            }

            if ((!data.result) || ((data.result)&&(data.finished)))
            {
                window['infiniteScroll'+sInfiniteScrollContainerName]['bHaveNext']=false;
            }
            $('#infiniteScrollLoadingRefreshSpin_'+sInfiniteScrollContainerName).hide();
        },
        error: function() {
            $('#infiniteScrollDisplayContent_'+sInfiniteScrollContainerName).append('<div class="alert alert-danger"> <strong>Error</strong> loading data  </div>');
            return false;
        }
    });

}
