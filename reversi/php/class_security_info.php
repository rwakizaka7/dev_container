<?php
require_once "class_utils.php";
class SecurityInfo
{
    public static function getDBConnectionInfo()
    {
        switch (Utils::getDBConnectionType())
        {
            case "local":
                return array("dns" => "mysql:host=db;dbname=docker_db;",
                    "username" => "user", "password" => "user");
        }
    }
}
?>