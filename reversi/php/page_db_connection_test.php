<?php
// http://localhost:8080/reversi/php/page_db_connection_test.php
// http://localhost:3000
require_once "include_security_review.php";
require_once "class_security_info.php";
$info = SecurityInfo::getDBConnectionInfo();
try {
    $sql = "SELECT * FROM `TEST1_TABLE`;";
    $statement = (new PDO($info["dns"], $info["username"], $info["password"]))->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    var_dump($result);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>