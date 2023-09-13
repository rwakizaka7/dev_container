<?php
// curl --include "http://localhost:8080/reversi/php/api2_get_test1_table.php?colum1=3"
// curl --include "https://wakizaka24.sakura.ne.jp/reversi/php/api2_get_test1_table.php?colum1=3"
// http://localhost:3000

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

try {
    $colum1 = $_GET["colum1"];
    if (!$colum1) {
        http_response_code(400);
        exit;
    }

    $sql = "SELECT * FROM TEST1_TABLE WHERE COLUM1 = :COLUM1;";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":COLUM1", $colum1, PDO::PARAM_INT);
    $response = $statement->execute();
    if ($response) {
        $data = $statement->fetch();
    }

    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, "
        ."Access-Control-Allow-Headers, Authorization, X-Requested-With");
    echo json_encode(array("data"=>$data ?: json_encode([], JSON_FORCE_OBJECT)),
        JSON_UNESCAPED_UNICODE);

    http_response_code(200);
} catch (PDOException $e) {
    setDBErrorJson($e);
}
?>