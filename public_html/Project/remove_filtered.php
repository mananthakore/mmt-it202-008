<?php
require(__DIR__ . "/../../partials/nav.php");

$db = getDB();

$query = "UPDATE `UserTeams` SET is_active = 0 WHERE 1=1";
$params = [];

if (!empty($_GET['username'])) {
    $query .= " AND user_id IN (SELECT id FROM `Users` WHERE username LIKE :username)";
    $params[":username"] = "%" . $_GET['username'] . "%";
}

if (!empty($_GET['name'])) {
    $query .= " AND team_id IN (SELECT id FROM `NBA_Teams` WHERE name LIKE :name)";
    $params[":name"] = "%" . $_GET['name'] . "%";
}
if (!empty($_GET['nickname'])) {
    $query .= " AND team_id IN (SELECT id FROM `NBA_Teams` WHERE nickname LIKE :nickname)";
    $params[":nickname"] = $_GET['nickname'];
}
if (!empty($_GET['city'])) {
    $query .= " AND team_id IN (SELECT id FROM `NBA_Teams` WHERE city LIKE :city)";
    $params[":city"] = $_GET['city'];
}

if (!empty($_GET['logo'])) {
    $query .= " AND team_id IN (SELECT id FROM `NBA_Teams` WHERE logo LIKE :logo)";
    $params[":logo"] = $_GET['logo'];
}

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    flash("Filtered associations removed successfully", "success");
} catch (PDOException $e) {
    error_log("Error removing filtered associations: " . $e->getMessage());
    flash("Error removing filtered associations", "danger");
}

redirect("admin/team_associations.php");
?>