<?php
/*
curl --include "http://localhost:8080/reversi/php/api3_post_test1_table.php" \
-X POST -H "Content-Type: application/json" \
-d '{
    "table1_values": [
        {
            "COLUM1": "3",
            "COLUM2": "Value 3"
        }
    ]
}'

curl --include "https://wakizaka24.sakura.ne.jp/reversi/php/api3_post_test1_table.php" \
-X POST -H "Content-Type: application/json" \
-d '{
    "table1_values": [
        {
            "COLUM1": "3",
            "COLUM2": "Value 3"
        }
    ]
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

$key = "table1_values";
$bodyStr = file_get_contents("php://input");
//var_dump($bodyStr);
$body = json_decode($bodyStr, true);

if (!array_key_exists($key, $body)) {
    http_response_code(400);
    exit;
}

$table1Values = $body[$key];
if (count($table1Values) == 0) {
    http_response_code(400);
    exit;
}  

try {
    $pdo->beginTransaction();

    foreach($table1Values as $i => $colums) {
        $sql = "INSERT INTO `TEST1_TABLE`(`COLUM1`, `COLUM2`) VALUES(:COLUM1, :COLUM2);";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(":COLUM1", $colums["COLUM1"], PDO::PARAM_INT);
        $statement->bindParam(":COLUM2", $colums["COLUM2"], PDO::PARAM_STR);
        $statement->execute();
    }

    $pdo->commit();

    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, "
        ."Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    http_response_code(200);
} catch (PDOException $e) {
    $pdo->rollBack();
    setDBErrorJson($e);
}
?>