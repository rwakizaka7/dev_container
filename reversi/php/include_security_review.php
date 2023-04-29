<?php
require_once 'class_utils.php';
require_once 'class_security_info.php';
$maximumNumberOfAccesses = 300;
$info = SecurityInfo::getDBConnectionInfo();
$ip = Utils::getIP();
$uri = Utils::getURI();
$pdo = new PDO($info['dns'], $info['username'], $info['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// アクセスログを追加する
try {
    $pdo->beginTransaction();

    $sql = 'INSERT INTO REVERSI_ACCESS_LOG(IP, URI, RESTRICTION) VALUES(:IP, :URI, FALSE);';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':IP', $ip, PDO::PARAM_STR);
    $statement->bindParam(':URI', $uri, PDO::PARAM_STR);
    $statement->execute();
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    exit;
}

/*
調査用SQL
アクセス件数
SELECT `IP`, DATE_FORMAT(`ACCESS_DATE`, '%Y-%m-%d %H') AS DATE, COUNT(*) AS COUNT
FROM `REVERSI_ACCESS_LOG`
GROUP BY `IP`, DATE
HAVING COUNT(*) > 1
*/

// 1時間単位のアクセスログ数からアクセス制限を追加する
try {
    $sql = "INSERT INTO `REVERSI_ACCESS_RESTRICTION`(`IP`)
    SELECT DISTINCT `A`.`IP` FROM (SELECT `IP`
    FROM `REVERSI_ACCESS_LOG` `A`
    GROUP BY `IP`, DATE_FORMAT(`ACCESS_DATE`, '%Y-%m-%d %H')
    HAVING COUNT(*) > :MAXIMUN_NUMBER_OF_ACCESSES) `A`
    WHERE NOT EXISTS
    (SELECT * FROM `REVERSI_ACCESS_RESTRICTION` `B`
    WHERE `A`.`IP` = `B`.`IP`);";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':MAXIMUN_NUMBER_OF_ACCESSES',
        $maximumNumberOfAccesses, PDO::PARAM_INT);
    $statement->execute();
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    exit;
}

// アクセス制限時アクセス制限しアクセスログを更新する
try {
    $sql = "SELECT * FROM `REVERSI_ACCESS_RESTRICTION`
    WHERE `IP` = :IP;";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':IP', $ip, PDO::PARAM_STR);
    $statement->execute();
    if ($statement->rowCount() > 0) {
        $sql = "UPDATE `REVERSI_ACCESS_LOG` `A`
        SET `RESTRICTION` = TRUE
        WHERE EXISTS(
        SELECT ID FROM (SELECT MAX(ID) AS `ID` FROM `REVERSI_ACCESS_LOG`) `B`
        WHERE `B`.`ID` = `A`.`ID`)";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $pdo->commit();

        http_response_code(403);
        exit;
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    exit;
}
?>