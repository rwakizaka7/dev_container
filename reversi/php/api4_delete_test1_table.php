<?php
/*
curl --include "http://localhost:8080/reversi/php/api4_delete_test1_table.php" \
-X POST -H "Content-Type: application/json" \
-d '{
    "table1_deleting_keys": [3]
}'

curl --include "https://wakizaka24.sakura.ne.jp/reversi/php/api4_delete_test1_table.php" \
-X POST -H "Content-Type: application/json" \
-d '{
    "table1_deleting_keys": [3]
}'

http://localhost:3000
*/

/*
-- DROP TABLE IF EXISTS `TEST1_TABLE`;
CREATE TABLE `TEST1_TABLE` (
  `COLUM1` INT NOT NULL PRIMARY KEY,
  `COLUM2` VARCHAR(256) NULL
);
CREATE INDEX `TEST1_TABLE_COLUM1_INDEX`
ON `TEST1_TABLE`(`COLUM1`);
*/

require_once "include_security_review.php";
require_once "class_utils.php";
require_once "class_security_info.php";
$info = SecurityInfo::getDBConnectionInfo();
$pdo = new PDO($info["dns"], $info["username"], $info["password"],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

$key = "table1_deleting_keys";
$bodyStr = file_get_contents("php://input");
//var_dump($bodyStr);
$body = json_decode($bodyStr, true);

if (!array_key_exists($key, $body)) {
    http_response_code(400);
    exit;
}

$deletingKeys = $body[$key];
if (count($deletingKeys) == 0) {
    http_response_code(400);
    exit;
}  

try {
    $pdo->beginTransaction();

    foreach($deletingKeys as $i => $key) {
        $sql = "DELETE FROM `TEST1_TABLE` WHERE COLUM1=:COLUM1;";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(":COLUM1", $key, PDO::PARAM_INT);
        $statement->execute();
    }

    $pdo->commit();

    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Allow-Headers: Content-Type, "
        ."Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    http_response_code(200);
} catch (PDOException $e) {
    $pdo->rollBack();
    setDBErrorJson($e);
}
?>