<?php
// curl --include "http://localhost:8080/reversi/php/api1_get_unique_id.php"
// http://localhost:3000
require_once 'include_security_review.php';
require_once 'class_utils.php';
require_once 'class_security_info.php';
$retryCount = 3;
$info = SecurityInfo::getDBConnectionInfo();
$pdo = new PDO($info['dns'], $info['username'], $info['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->beginTransaction();
    
    $i = 0;
    while ($i < $retryCount) {
        $uniqid = uniqid("", true);
        $sql = 'SELECT * FROM REVERSI_PLAYER WHERE PLAYER_ID = :PLAYER_ID;';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':PLAYER_ID', $uniqid, PDO::PARAM_STR);
        $statement->execute();
        if ($statement->rowCount() <= 0) {
            break;
        }
        $i++;
    }
    if ($i == $retryCount) {
        throw new Exception("ユニークIDの生成に失敗しました");
    }
    $ip = Utils::getIP();

    $sql = 'INSERT INTO REVERSI_PLAYER(PLAYER_ID, IP) VALUES(:PLAYER_ID, :IP);';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':PLAYER_ID', $uniqid, PDO::PARAM_STR);
    $statement->bindParam(':IP', $ip, PDO::PARAM_STR);
    $res = $statement->execute();
    $pdo->commit();

    http_response_code(200);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array('unique_id'=>$uniqid), JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $pdo->rollBack();
    
    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array('code'=>500, 'message'=>'DBの実行に失敗しました',
        'description'=>$e->getMessage()), JSON_UNESCAPED_UNICODE);
}
?>