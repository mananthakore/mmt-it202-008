<?php
require(__DIR__ . "/../../../lib/functions.php"); // mmt 4/30/24
session_start();
if (isset($_GET["team_id"]) && is_logged_in()) {
    $db = getDB();
    $query = "INSERT INTO `UserTeams` (user_id, team_id) VALUES (:user_id, :team_id)";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id" => get_user_id(), ":team_id" => $_GET["team_id"]]);
        flash("You've successfully favorited the team", "success");
        redirect("my_teams.php");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] === 1062) {
            flash("Team already favorited", "danger");
        } else {
            flash("Unhandled error occurred", "danger");
        }
        error_log("Error favoriting team: " . var_export($e, true));
    }
}
redirect("teams.php");