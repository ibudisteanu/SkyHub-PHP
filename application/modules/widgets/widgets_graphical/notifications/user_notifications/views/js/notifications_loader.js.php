var iLastNotificationDateSec = -1000;

function initializeNewerUserNotifications(iLastNotificationDateSecParam){
    iLastNotificationDateSec = iLastNotificationDateSecParam;
}

function getNewerUserNotifications()
{
    if (iLastNotificationDateSec === -1000) return ;

    $.ajax(
        {
            url: document.location.origin+"/api/notifications/post/get-newer-notifications/",
            type: 'POST',
            data:
                {
                    iLastNotificationDateSec : iLastNotificationDateSec,
                },
            success: function( outputJSONData )
            {
                var outputData = $.parseJSON(outputJSONData);

                if ((outputData.result == true)){

                    if (outputData.newerNotificationsCount > 0)
                    {
                        $("#userNotificationsContentList").append(outputData.newerNotificationsHTMLEmbeddedCode);

                        iLastNotificationDateSec = outputData.iNewerLastNotificationDateSec;
                    }

                    refreshTotalNewNotifications(outputData.iTotalNewNotifications);

                }
                else {//Error
                    console.log(outputData.message);
                }

                setTimeout(function(){getNewerUserNotifications()},5000);
            },
            error: function() {
                console.log('internal error getting notifications');
                setTimeout(function(){getNewerUserNotifications()},5000);
            }
        });
}

function refreshTotalNewNotifications(iNewValueNotifications)
{
    $labelSpan = $("#userNotificationsLabelSpan");
    $labelWindow = $("#userNotificationsLabelWindow");

    if (iNewValueNotifications > 0)
    {
        $labelSpan.show();
        $labelSpan.html(iNewValueNotifications);
        $labelWindow.html("You have <b>"+iNewValueNotifications+"</b> new notifications");
    } else
    {
        $labelSpan.hide();
        $labelWindow.html("You don't have any new notifications");
    }
}

function openNotificationsWindow($object)
{
    /*$object = $($object);
     if ($object.attr('aria-expanded')=='true')
     refreshTotalNewNotifications(0);*/
    viewedNewerNotifications();
}

function viewedNewerNotifications()
{
    $.ajax(
        {
            url: document.location.origin+"/api/notifications/post/viewed-newer-notifications/",
            type: 'POST',
            data:
                {
                    iLastNotificationDateSec : iLastNotificationDateSec,
                },
            success: function( outputJSONData )
            {
                var outputData = $.parseJSON(outputJSONData);

                if ((outputData.result == true)){

                    refreshTotalNewNotifications(0);
                }
                else {//Error
                    console.log(outputData.message);
                }

            },
            error: function() {
                console.log('internal resetting new notifications');
            }
        });
}

setTimeout(function(){getNewerUserNotifications()},15000);