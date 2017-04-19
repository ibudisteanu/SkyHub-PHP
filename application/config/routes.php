<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

if ( ! function_exists('createUndefinedRoutes')) {
    function createUndefinedRoutes(&$route, $sRoutePrefix, $sRouteValue, $iIndexCount = 15)
    {
        for ($index = 1; $index < $iIndexCount; $index++) {
            $sRoutePrefix .= '/(:any)';
            $sRouteValue .= '/$' . $index;
            $route[$sRoutePrefix] = $sRouteValue;
        }
    }
}

$route['404_override'] = '404/my404/index';
$route['translate_uri_dashes'] = FALSE;

/*HOME*/
$route['default_controller'] = 'pages/Home/index';
$route['index.htm'] = 'pages/Home/index';

$route['disable-analytics'] = 'pages/Home/disableAnalytics';

$route['privacy-policy'] = 'pages/Privacy_policy_controller/index';
$route['terms-conditions'] = 'pages/Terms_and_conditions_controller/index';

$route['page'] = 'pages/Home/index';
$route['page/(:any)'] = 'pages/Home/index/$1';

$route['all-topics'] = 'pages/All_topics_page_controller/index';

$route['oauth-homepage/(:any)/(:any)'] = 'pages/Home/oauthBugHomePage/$1/$2';

$route['admin'] = 'dashboard/dashboard/index';

$route['admin/(:any)'] = 'dashboard/dashboard/index/$1';
$route['admin/apps/(:any)'] = 'dashboard/dashboard/index/apps/$1';
$route['admin/apps/(:any)/(:any)'] = 'dashboard/dashboard/index/apps/$1/$2';

$route['admin/site/categories'] = 'dashboard/site_categories_board/index';
$route['admin/site/categories/add-category'] = 'dashboard/site_categories_board/index/add-category';
$route['admin/site/categories/(:any)/(:any)'] = 'dashboard/site_categories_board/index/$1/$2';

$route['admin/site/emails'] = 'dashboard/emails_board/index';
$route['admin/site/emails/add-category'] = 'dashboard/emails_board/index/#AddSiteCategory';
$route['admin/site/emails/(:any)/(:any)'] = 'dashboard/emails_board/index/$1/$2';


$route['login'] = 'pages/signin/index';
$route['register'] = 'pages/signin/index';
$route['logout'] = 'auth_site/login/logout';
$route['about'] = 'contact/about/index/';
$route['contact'] = 'contact/about/index/#Contact';

$route['popup/login/test'] = 'popup_auth/popup_authentication/test_index';

$route['profile/edit'] = 'profile/edit_profile/index';
$route['profile/edit/(:any)'] = 'profile/edit_profile/index/$1';

$route['profile'] = 'profile/view_profile/index';
$route['profile/(:any)'] = 'profile/view_profile/index/$1';
$route['profile/(:any)/(:any)'] = 'profile/view_profile/index/$1/$2';

createUndefinedRoutes($route, 'test', 'pages/Home/test', 15);
createUndefinedRoutes($route, 'category', 'site_category/view_site_category/index', 15);
createUndefinedRoutes($route, 'forum/category', 'forum_category/view_forum_category/index', 15);
createUndefinedRoutes($route, 'forum', 'forum/view_forum/index', 15);
createUndefinedRoutes($route, 'topic', 'topic/view_topic/viewPage', 15);

//infinite scroll

//reply submit request

$route['api/crawler/news/forum-categories'] =  'crawler/api_crawler/getAllForumsNewsTags';
$route['api/crawler/news/site-categories'] =  'crawler/api_crawler/getAllSiteCategoriesNewsTags';
$route['api/crawler/news/all-categories'] =  'crawler/api_crawler/getAllDataForCrawler';

$route['api/crawler/topics/all-topics-urls'] =  'crawler/api_crawler/getAllTopicsURLs';

//-------------------------------------------------CRON JOBS------------------------------------------------------------

$route['cron-jobs/emails/execute'] =  'cron_jobs/cron_emails_controller/runCronJob/true/true/true';
$route['cron-jobs/emails/execute/unsent'] =  'cron_jobs/cron_emails_controller/runCronJob/true/false';
$route['cron-jobs/emails/execute/error'] =  'cron_jobs/cron_emails_controller/runCronJob/false/true';

//---------------------------------------------------APIs---------------------------------------------------------------

//-----------------------------------USERS AUTHENTICATION & REGISTRATION------------------------------------------------
$route['api/users/post/authentication/login'] =  'api/api_signin/loginFastPOST';
$route['api/users/get/authentication/login/(:any)/(:any)'] =  'api/api_signin/loginFast/$1/$2';
$route['api/users/get/authentication/logout'] =  'api/api_signin/logoutFast';

$route['api/users/post/registration/check-used-email'] =  'api/api_registration/checkEmailUsedPOST';
$route['api/users/post/registration/check-used-username'] =  'api/api_registration/checkUsernameUsedPOST';

//-----------------------------------------------VOTING SYSTEM----------------------------------------------------------

$route['api/voting/post/processVoteSubmit'] =  'api/api_voting/processVoteSubmit';

//--------------------------------------------------REPLIES-------------------------------------------------------------

$route['api/reply/post/processReplySubmit'] =  'api/api_replies/processReplySubmit';

//--------------------------------------------------TOPICS-------------------------------------------------------------

$route['api/topic/post/add/(:any)'] =  'api/api_topics/addTopicFast/$1';
$route['api/topic/post/add/(:any)/(:any)'] =  'api/api_topics/addTopicFast/$1/$2';
$route['api/topic/post/show-form'] =  'api/api_topics/showTopicForm';
$route['api/topic/post/delete'] =  'api/api_topics/deleteTopic';

//-------------------------------------------------FILE UPLOAD----------------------------------------------------------

$route['api/files-upload/upload/(:any)'] =  'api/api_file_upload/fileUpload/$1';

//----------------------------------------------------CHAT--------------------------------------------------------------

$route['api/chat/get/get-chat-embedded-code/(:any)'] = 'api/api_chat/getChatEmbeddedCode/$1';
$route['api/chat/get/get-create-conversation-id-with-authors'] = 'api/api_chat/getCreateConversationIDWithAuthors';
$route['api/chat/get/refresh-chats'] = 'api/api_chat/getRefreshChats';
$route['api/chat/post/post-message-chat'] = 'api/api_chat/postMessageChat';
$route['api/chat/post/reset-new-messages-notification'] = 'api/api_chat/resetNewMessagesNotification';
$route['api/chat/post/change-maximization-chat-dialog-status'] = 'api/api_chat/changeMaximizationChatDialogStatus';
$route['api/chat/post/close-chat-conversation'] = 'api/api_chat/closeChatConversation';

//---------------------------------------------------CONTENT------------------------------------------------------------

$route['api/content/post/getTopContent/(:any)'] =  'api/api_display_top_content/getTopContentJSON/$1';
$route['api/content/post/getTopContent/(:any)/(:any)'] =  'api/api_display_top_content/getTopContentJSON/$1/$2';
$route['api/content/post/getTopContent/(:any)/(:any)/(:any)'] =  'api/api_display_top_content/getTopContentJSON/$1/$2/$3';

//---------------------------------------------------NOTIFICATIONS------------------------------------------------------------

$route['api/notifications/post/get-newer-notifications'] =  'api/api_notifications/getNewerNotifications';
$route['api/notifications/post/viewed-newer-notifications'] =  'api/api_notifications/viewedNewerNotifications';

//-------------------------------------------------END APIs-------------------------------------------------------------


$route['app/res/js/(:any)/(:any)/(:any)/(:any)/(:any)'] =  'app_resources/app_resources/loadJSResource/$1/$2/$3/$4/$5';
$route['app/res/js/(:any)/(:any)/(:any)/(:any)'] =  'app_resources/app_resources/loadJSResource/$1/$2/$3/$4';
$route['app/res/js/(:any)/(:any)/(:any)'] =  'app_resources/app_resources/loadJSResource/$1/$2/$3';
$route['app/res/js/(:any)/(:any)'] =  'app_resources/app_resources/loadJSResource/$1/$2';
$route['app/res/js/(:any)'] =  'app_resources/app_resources/loadJSResource/$1';
$route['app/res/css/(:any)'] =  'app_resources/app_resources/loadCSSResource/$1';

$route['emails/view'] = 'emails/email_controller/index';
$route['emails/view/(:any)'] = 'emails/email_controller/index/$1';

$route['login/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1';
$route['login/(:any)/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1/$2';
$route['signin/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1';
$route['signin/(:any)/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1/$2';
$route['signup/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1';
$route['signup/(:any)/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1/$2';

$route['register/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1';
$route['register/(:any)/(:any)'] = 'oauth2/Oauth2_controller/processOAuth2/$1/$2';

$route['awesome'] = 'pages/pages/awesome';
$route['zzz'] = 'Welcome';
$route['zzzz'] = 'Welcome/index2';

//-------------------------------------------------UNIT TESTING---------------------------------------------------------
$route['app/testing/email'] = 'emails/email_controller/unitTestingEmail';