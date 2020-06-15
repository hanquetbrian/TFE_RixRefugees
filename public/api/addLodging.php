<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";
session_start();

require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);

$result = [];

$name = htmlspecialchars($_POST['name']);
$nbPlace = htmlspecialchars($_POST['nb_place']);
$dateFrom = htmlspecialchars($_POST['date_from']);
$dateTo = htmlspecialchars($_POST['date_to']);
$address = htmlspecialchars($_POST['address']);
$filename = '/img/house.jpg';

if(isset($_FILES['image'])) {
    $uploadOk = true;
    $file =  $_FILES['image']['name'];
    /* Valid Extensions */
    $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
    $valid_extensions = array("jpg","jpeg","png", "gif");
    /* Check file extension */
    if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
        $uploadOk = false;
    }

    if($uploadOk) {
        while (true) {
            $filename = uniqid('/upload/', true);
            if (!file_exists($filename)) break;
        }
        $filename .= "." . $imageFileType;

        if(!move_uploaded_file($_FILES['image']['tmp_name'], ".." . $filename)){
            $result["error"]["type"] = "error upload";
            $result["error"]["msg"] = "Could not upload the file to the server";
        }
    } else {
        $result["error"]["type"] = "error upload";
        $result["error"]["msg"] = "Incorrect extension used";
    }

}

// insert data in the database
$dbh->beginTransaction();
$sql = "INSERT INTO rix_refugee.Lodging (lodging_name, address, nb_place, pic_url) VALUES (:lodging_name, :address, :nb_place, :pic_url);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->execute(array(
    ':lodging_name' => $name,
    ':address' => $address,
    ':nb_place' => $nbPlace,
    ':pic_url' => $filename,
    ));

$sql = "INSERT INTO rix_refugee.Lodging_session (lodging_id, date_from, date_to, coordinator_id) VALUES (:lodging_id, :date_from, :date_to, :coordinator_id);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->execute(array(
    ':lodging_id' => $dbh->lastInsertId(),
    ':date_from' => $dateFrom,
    ':date_to' => $dateTo,
    ':coordinator_id' => $AUTH->getCoordId(),
));

if(isset($_POST['equipments'])) {
    $lodgingId = $dbh->lastInsertId();
    $sth->closeCursor();
    $sql = "INSERT INTO Lodging_equipment (lodging_id, equipment_name) VALUES ($lodgingId, :equipment_name)";
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

    foreach (explode(',',$_POST['equipments']) as $equipment) {
        $sth->execute(array(':equipment_name' => htmlspecialchars($equipment)));
    }
}

$sth->closeCursor();

$dbh->commit();

$result["success"] = true;
echo json_encode($result);