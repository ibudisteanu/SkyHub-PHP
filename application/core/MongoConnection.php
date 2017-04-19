<?php

class MongoConnection
{

    static $dbMongo = null;
    static $dbMongoDB = null;

    static $sMongoDataBaseName;
    static $arrMongoCollectionsOpened = [];

    protected static function createMongoConnection()
    {
        register_shutdown_function('shutdownMongoConnections');

        if (MongoConnection::$dbMongo == null)
        {
            $options = array("connectTimeoutMS" => 30000, "replicaSet" => "replicaSetName");

            try
            {
                //Localhost database
                if (defined('DATABASE_OFFLINE'))
                {
                    MongoConnection::$dbMongo = new MongoClient();
                    MongoConnection::$sMongoDataBaseName = LOCAL_MONGODB_DB_NAME;
                } else
                {
                    //online database
                    MongoConnection::$dbMongo = new MongoClient("mongodb://".REMOTE_MONGODB_USERNAME.":".REMOTE_MONGODB_PASSWORD."@".REMOTE_MONGODB_REMOTE_ADDRESS.":".REMOTE_MONGODB_PORT."/".REMOTE_MONGODB_DB_NAME);
                    MongoConnection::$sMongoDataBaseName = REMOTE_MONGODB_DB_NAME;
                }

                if (MongoConnection::$dbMongo != null) {
                    $sMongoDataBaseName = MongoConnection::$sMongoDataBaseName;
                    MongoConnection::$dbMongoDB = MongoConnection::$dbMongo->{$sMongoDataBaseName};
                }

            }
            catch (Exception $exception)
            {
                echo "SkyHub: <strong>Internal Error</strong><br/><strong>Couldn't connect to the database</strong><br/> Please contact the Administrator of SkyHub <br/> alexandru [at] skyhub [.] me <br/>";
                echo "<br/> <br/>".$exception->getMessage() . "<br/>";
                exit;
            }
        }
    }

    public static function selectCollection($sCollectionName)
    {
        try
        {
            if (MongoConnection::$dbMongo == null) MongoConnection::createMongoConnection();

            if (MongoConnection::$dbMongoDB != null) {

                $collection = MongoConnection::findCollection($sCollectionName);
                if ($collection != null) return $collection;
                else{
                    $collection = MongoConnection::$dbMongoDB->{$sCollectionName};

                    if ($collection != null) {
                        array_push(MongoConnection::$arrMongoCollectionsOpened, [ 'name' => $sCollectionName, 'collection' => $collection ]);
                    }

                    return $collection;
                }
            }
            else
                return null;
        }
        catch (Exception $exception)
        {
            echo "Couldn't select the Collection <strong>".$sCollectionName.'</strong> <br/>';
            echo "<br/> <br/>".$exception->getMessage() . "<br/>";
        }
    }

    protected static function findCollection($sName)
    {
        foreach (MongoConnection::$arrMongoCollectionsOpened as $openedCollection)
            if ($openedCollection['name'] == $sName)
                return $openedCollection['collection'];

        return null;
    }

}

function shutdownMongoConnections()
{
    MongoConnection::$dbMongo->close();
}