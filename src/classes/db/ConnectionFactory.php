<?php

namespace iutnc\netvod\db;
use PDO;
ConnectionFactory::setConfig( './conf/conf.ini' );

class ConnectionFactory{
    public static $db = null;
    public static $config =[];



    /**
     * @param $iniFile
     * @return void
     */

    public static function setConfig($iniFile): void{
        self::$config = parse_ini_file($iniFile);
    }



    /**
     * @return PDO|null
     */

    public static function makeConnection(){
        if(self::$db === null){
            $dsn = self::$config['driver'].
                ':host='.self::$config['host'].
                ';dbname='.self::$config['dataBase'];
            self::$db = new PDO($dsn, self::$config['username'], self::$config['password'],[
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
            self::$db->prepare("SET NAMES 'utf8'")->execute();
        }
        return self::$db;
    }
}