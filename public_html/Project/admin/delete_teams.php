<?php

session_start();
require_once(__DIR__ . "/../../../lib/functions.php");

// Check if the user has admin role, redirect if not
if (!has_role("Admin")) {
    flash("You do not have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);

// Validate if the id is valid
if ($id < 1) {
    flash("Invalid id passed to delete", "danger");
    die(header("Location: " . get_url("admin/list_teams.php")));
}

// Delete team record
$db = getDB();
$query = "DELETE FROM `NBA_Teams` WHERE id=:id";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id]);
    flash("Deleted record with id $id", "success");
} catch (PDOException $e) {
    error_log("Error deleting team $id: " . var_export($e, true));
    flash("Error deleting record", "danger");
}

// Redirect to the list of teams page
die(header("Location: " . get_url("admin/list_teams.php")));
