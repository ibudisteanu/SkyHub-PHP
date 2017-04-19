<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Advanced_cache extends CI_Model
{
    public $bAllowMinimizingCache = true;
    public function __construct()
    {
        $this->initiateCache();
    }

    private function initiateCache()
    {
        if (defined('WEBSITE_OFFLINE'))
        {
            //$this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
            $this->load->driver('cache', array('adapter' => 'file'));
        } else
        {
            //$this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
            $this->load->driver('cache', array('adapter' => 'file'));
        }
        //$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function rewriteCachedObject($sCacheId, $newObject, $sCachedObjectPropertyName='', $newObjectComparisionPropertyName='sID', $bDeletion=false)
    {
        if (is_bool($sCachedObjectPropertyName)) {
            $bDeletion = $sCachedObjectPropertyName;
            $sCachedObjectPropertyName = '';
        }

        $cache = $this->get($sCacheId);//getting the old cache

        if ($cache == false) return false;//no cache - no update

        //echo 'rewriteCachedObject: '.$sCacheId.' '.$sCachedObjectPropertyName.' '.(string)$bDeletion;
        //var_dump($cache);
        if ($sCachedObjectPropertyName!= '')//Property from the CachedObject (sub-element)
        {

            if (isset($cache->{$sCachedObjectPropertyName}))
                $cacheObject = $cache->{$sCachedObjectPropertyName};
            else {
                //no such property
                echo 'NO SUCH CACHE PROPERTY ' . $sCachedObjectPropertyName;
                return false;
            }
        } else//entire Cached Object
            $cacheObject = $cache;

        if ($cacheObject == null) return false;

        $bRewritten=false;
        //var_dump($newObject);
        if (is_array($cacheObject))
        {
            $bClassSame=true;

            $index=-1;
            while ($index+1 < count($cacheObject)) {
                $index++;
                $element = $cacheObject[$index];
                //echo get_class($element). ' '.get_class($newObject). ' ';
                //echo $element->{$newObjectComparisionPropertyName}.' '.$newObject->{$newObjectComparisionPropertyName};

                if (($element != null)&&(get_class($element) == get_class($newObject)) && ($element->{$newObjectComparisionPropertyName} == $newObject->{$newObjectComparisionPropertyName})) {

                    if ($bDeletion) // has been deleted
                        unset($cacheObject[$index]);
                    else
                        $cacheObject[$index] = $newObject;

                    $bRewritten = true;
                    break;
                }
                if (get_class($element) != get_class($newObject)) $bClassSame = false;
            }

            if (($bClassSame)&&(!$bRewritten)&&(!$bDeletion))//was not found there
            {
                array_push($cacheObject, $newObject);
                $bRewritten = true;
            }

        } else
        {
            if ((!is_object($cacheObject)) || (!is_object($newObject)))
                return false;

            if ((get_class($cacheObject) == get_class($newObject)) && ($cacheObject->{$newObjectComparisionPropertyName} == $newObject->{$newObjectComparisionPropertyName})) {

                if ($bDeletion) // has been deleted
                {
                    $this->delete($sCacheId);
                    return true;
                }
                else
                    $cacheObject = $newObject;
                $bRewritten = true;
            }
        }

        if ($bRewritten)
        {
            if ($sCachedObjectPropertyName != '')
                $cache->{$sCachedObjectPropertyName} = $cacheObject;
            else
                $cache = $cacheObject;

            $this->save($sCacheId,  $cache);
            return true;
        } else return false;
    }

    public function get($sID)
    {
        return $this->cache->get($sID);
    }

    public function save($sCacheId, $result=null, $iTime=2678400)
    {

        if (!$this->bAllowMinimizingCache)
            return $this->cache->save($sCacheId, $result, $iTime);

        if (($result!= null)&&(is_array($result)))
        {
            foreach ($result as $element)
                $element->callDestructorCached();

            $return = $this->cache->save($sCacheId, $result, $iTime);

            foreach ($result as $element)
                $element->retrieveBackDestructorCached();

            return $return;
        }
        else
            if ((is_object($result))&&(method_exists($result,'callDestructorCached'))) {
                $result->callDestructorCached();

                $return = $this->cache->save($sCacheId, $result, $iTime);

                $result->retrieveBackDestructorCached();

                return $return;
            }

        return $this->cache->save($sCacheId, $result, $iTime);

       /* if (!$this->bAllowMinimizingCache)
            return $this->cache->save($sCacheId, $result, $iTime);

        if (($result!= null)&&(is_array($result)))
        {
            $resultCloned = [];
            foreach ($result as $element) {
                $elementCloned = clone $element;
                $elementCloned->callDestructorCached();

                array_push($resultCloned, $elementCloned);
            }
            return $this->cache->save($sCacheId, $resultCloned, $iTime);
        }
        else
            if ((is_object($result))&&(method_exists($result,'callDestructorCached'))) {
                $resultCloned = clone $result;
                $resultCloned->callDestructorCached();

                return $this->cache->save($sCacheId, $resultCloned, $iTime);
            }

        return $this->cache->save($sCacheId, $result, $iTime);*/
    }

    public function delete($sCacheID)
    {
        try {
            return $this->cache->delete($sCacheID);
        }
        catch (Exception $exception) {

        }
    }


    public function getIDFromFullURL($sFullURL, $sObjectType='')
    {
        /*
         * The URL has been ENCODED because the Cache couldn't store the URL
         */
        $sCacheId = 'getIDFromFullURL_'.urlencode($sFullURL);

        if (($sCacheId=='')||(!$sResultId  = $this->AdvancedCache->get($sCacheId)))
        {
            $sFoundID='';
            if (($sFoundID=='')&&(($sObjectType == '')||($sObjectType=='forum')))
            {
                $this->load->model('forum/Forums_model','ForumsModel');
                $forum = $this->ForumsModel->findForumByFullURL($sFullURL);
                if (($forum != null)&&(is_object($forum)))  $sFoundID =  $forum->sID;
            }

            if (($sFoundID=='')&&(($sObjectType == '')||($sObjectType=='topic')))
            {
                $this->load->model('topics/Topics_model','TopicsModel');
                $topic = $this->TopicsModel->findTopicByFullURL($sFullURL);
                if (($topic != null)&&(is_object($topic)))   $sFoundID =  $topic->sID;
            }

            if (($sFoundID=='')&&(($sObjectType == '')||($sObjectType=='forum_category')))
            {
                $this->load->model('forum_categories/forum_categories_model','ForumCategoriesModel');
                $forumCategory = $this->ForumCategoriesModel->findForumCategoryByFullURL($sFullURL);
                if (($forumCategory != null)&&(is_object($forumCategory)))   $sFoundID =  $forumCategory->sID;
            }

            if (($sFoundID=='')&&(($sObjectType == '')||($sObjectType=='site_category')))
            {
                $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
                $siteCategory = $this->SiteCategoriesModel->findCategoryFromFullURL($sFullURL);
                if (($siteCategory != null)&&(is_object($siteCategory)))   $sFoundID =  $siteCategory ->sID;
            }

            if (($sFoundID=='')&&(($sObjectType == '')||($sObjectType=='user')))
            {
                $this->load->model('users/users_minimal','UsersMinimal');
                $user = $this->UsersMinimal->findUserByFullURL($sFullURL);
                if (($user != null)&&(is_object($user)))   $sFoundID =  $user->sID;
            }

            $sResultId = $sFoundID;

            if (($sCacheId != '')&&($sResultId!=''))
                $this->save($sCacheId, $sResultId, 2678400);
        }

        return $sResultId;
    }

    public function getObjectTypeFromId($sId)
    {
        $sCacheId = 'getObjectTypeFromId_'.$sId;
        $sObjectType='';

        if (($sCacheId=='')||(!$sObjectType = $this->AdvancedCache->get($sCacheId))) {

            if (($sObjectType == ''))
            {
                $this->load->model('forum/Forums_model','ForumsModel');
                $forum = $this->ForumsModel->findForum($sId);
                if (($forum != null)&&(is_object($forum)))  $sObjectType =  'forum';
            }

            if ($sObjectType == '')
            {
                $this->load->model('topics/Topics_model','TopicsModel');
                $topic = $this->TopicsModel->getTopic($sId);
                if (($topic != null)&&(is_object($topic)))   $sObjectType =  'topic';
            }

            if ($sObjectType == '')
            {
                $this->load->model('forum_categories/forum_categories_model','ForumCategoriesModel');
                $forumCategory = $this->ForumCategoriesModel->getForumCategory($sId);
                if (($forumCategory != null)&&(is_object($forumCategory)))   $sObjectType =  'forum_category';
            }

            if ($sObjectType=='')
            {
                $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
                $siteCategory = $this->SiteCategoriesModel->findCategory($sId);
                if (($siteCategory != null)&&(is_object($siteCategory)))   $sObjectType = 'site_category';
            }

            if ($sObjectType=='')
            {
                $this->load->model('users/users_minimal','UsersMinimal');
                $user = $this->UsersMinimal->userByMongoId($sId);
                if (($user != null)&&(is_object($user)))   $sObjectType = 'user';
            }

            if (($sCacheId != '')&&($sObjectType!=''))
                $this->save($sCacheId, $sObjectType, 2678400);
        }

        return $sObjectType;
    }

    /*
     * Get Object (Forum, SiteCategory, ForumCategory, User)
     */

    public function getObjectFromId($sId, $sObjectType='')
    {
        $sCacheId = 'getObjectFromId_'.$sId;
        $objResultObject=null;

        if ($sObjectType == '') {
            $sObjectType = $this->getObjectTypeFromId($sId);

            if ($sObjectType == '')
                return null;
        }

        //It is required for the CACHE, otherways it returns me an error with INCOMPLETE_CLASS
        $this->load->model('forum/Forums_model','ForumsModel');
        $this->load->model('topics/Topics_model','TopicsModel');
        $this->load->model('forum_categories/forum_categories_model','ForumCategoriesModel');
        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
        $this->load->model('users/users_minimal','UsersMinimal');

        if (($sCacheId=='')||(!$objResultObject  = $this->AdvancedCache->get($sCacheId)))
        {
            $objFound=null;
            if (($objFound==null)&&(($sObjectType == '')||($sObjectType=='forum')))
            {
                //$this->load->model('forum/Forums_model','ForumsModel');
                $forum = $this->ForumsModel->findForum($sId);
                if (($forum != null)&&(is_object($forum)))  $objFound =  $forum;
            }

            if (($objFound==null)&&(($sObjectType == '')||($sObjectType=='topic')))
            {
                //$this->load->model('topics/Topics_model','TopicsModel');
                $topic = $this->TopicsModel->getTopic($sId);
                if (($topic != null)&&(is_object($topic)))   $objFound =  $topic;
            }

            if (($objFound==null)&&(($sObjectType == '')||($sObjectType=='forum_category')))
            {
                //$this->load->model('forum_categories/forum_categories_model','ForumCategoriesModel');
                $forumCategory = $this->ForumCategoriesModel->getForumCategory($sId);
                if (($forumCategory != null)&&(is_object($forumCategory)))   $objFound =  $forumCategory;
            }

            if (($objFound==null)&&(($sObjectType == '')||($sObjectType=='site_category')))
            {
                //$this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
                $siteCategory = $this->SiteCategoriesModel->findCategory($sId);
                if (($siteCategory != null)&&(is_object($siteCategory)))   $objFound =  $siteCategory;
            }

            if (($objFound==null)&&(($sObjectType == '')||($sObjectType=='user')))
            {
                //$this->load->model('users/users_minimal','UsersMinimal');
                $user = $this->UsersMinimal->userByMongoId($sId);
                if (($user != null)&&(is_object($user)))   $objFound =  $user;
            }

            $objResultObject = $objFound;

            if (($sCacheId != '')&&($objResultObject!=''))
                $this->save($sCacheId, $objResultObject, 2678400);
        } else
        {
            //cached already, but I need to call the constructor
            try {
                //var_dump($objResultObject);

                //if (method_exists($objResultObject, 'callConstructorsCached'))
                $objResultObject->callConstructorsCached($objResultObject);
            } catch (Exception $exception){
            }
        }

        return $objResultObject;
    }

    public function getObjectsFromIds($arrIds, $sObjectType='')
    {
        $arrResult = [];
        foreach ($arrIds as $id) {
            $object = $this->getObjectFromId($id, $sObjectType);

            if ($object != null)
                array_push($arrResult, $object);
        }

        return $arrResult;
    }

    public function resetFullURL($sFullURL)
    {
        $this->AdvancedCache->delete('getIDFromFullURL_'.$sFullURL);
    }

    public function clearCacheContainsWildcard($strPrefix )
    {
        $all_cache = $this->AdvancedCache->cache->cache_info();

        foreach ($all_cache as $cache_id => $cache) :
            if (strpos($cache_id, $strPrefix) !== false) :
                $this->delete($cache_id);
            endif;
        endforeach;
    }

    public function clean()
    {
        $this->cache->clean();
    }

}