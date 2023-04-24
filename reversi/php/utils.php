<?php
class Utils
{
    public static function getHTTPHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function getDBConnectionType()
    {
        switch (Utils::getHTTPHost()) {
            case 'localhost:8080':
                return 'local';
            case 'wakizaka24.sakura.ne.jp':
                return 'production';
        }
    }
}
?>