<?php
require_once 'security_info.php';
try {
    $info = SecurityInfo::getDBConnectionInfo();
    $sql = 'SELECT * FROM TEST1_TABLE;';
    $statement = (new PDO($info['dns'], $info['username'], $info['password']))->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    var_dump($result);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}
?>