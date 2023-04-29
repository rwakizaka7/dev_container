<?php
class Utils
{
    public static function getIP()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public static function getHTTPHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function getURI()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getDBConnectionType()
    {
        switch (Utils::getHTTPHost())
        {
            case 'localhost:8080':
                return 'local';
            case 'wakizaka24.sakura.ne.jp':
                return 'production';
        }
    }
}
?>