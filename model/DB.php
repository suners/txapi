<?php

namespace txapi\model;

include ROOT . '/model/TxPDO.php';

use txapi\model\TxPDO;

/**
 * DB
 * @author abin <rawuzebin@126.com>
 */
class DB
{

    public static $_DB_HOST = 'localhost';
    public static $_DB_USER = 'root';
    public static $_DB_PASS = 'root';
    public static $_DB_NAME = 'txapi';

    public static function model()
    {
        return TxPDO::getInstance(self::$_DB_HOST, self::$_DB_USER, self::$_DB_PASS, self::$_DB_NAME, 'utf8');
    }

}
