<?php
require_once 'class_security_info.php';
$uniqid = uniqid(rand().'_');
$info = SecurityInfo::getDBConnectionInfo();
try {
    $sql = 'SELECT * FROM TEST1_TABLE;';
    $statement = (new PDO($info['dns'], $info['username'], $info['password']))->prepare($sql);
    $statement->execute();
    
    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array('unique_id'=>$uniqid), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array('code'=>500, 'message'=>'DBの実行に失敗しました',
        'description'=>$e->getMessage()), JSON_UNESCAPED_UNICODE);
}
?>