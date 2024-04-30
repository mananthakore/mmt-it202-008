<?php

require(__DIR__ . "/../../../partials/nav.php"); // mmt 4/29/2024

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect('home.php');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["entities"]) && isset($_POST["users"])) {
        $entityIdentifiers = $_POST["entities"];
        $userIds = $_POST["users"];
        if (empty($entityIdentifiers) || empty($userIds)) {
            flash("Entities and users need to be selected", "warning");
        } else {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO UserTeams (user_id, team_id, is_active) VALUES (:userId, :teamId, 1) 
            ON DUPLICATE KEY UPDATE is_active = IF(is_active = 1, 0, 1)");
            foreach ($entityIdentifiers as $teamId) {
                foreach ($userIds as $userId) {
                    try {
                        $stmt->execute([":userId" => $userId, ":teamId" => $teamId]);
                        flash("Association updated", "success");
                    } catch (PDOException $e) {
                        flash(var_export($e->errorInfo, true), "danger");
                    }
                }
            }
        }
    }
}

$entities = [];
$entityIdentifier = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $entityIdentifier = $_POST["entity_identifier"] ?? "";

    if (!empty($entityIdentifier)) { // mmt 4/29/2024
        $db = getDB();
        $stmt = $db->prepare("SELECT id, name FROM `NBA_Teams` WHERE name LIKE :entityIdentifier LIMIT 25");
        
        try {
            $stmt->execute([":entityIdentifier" => "%$entityIdentifier%"]);
            $entities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Entity identifier cannot be empty", "warning");
    }
}

$users = [];
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";

    if (!empty($username)) { // mmt 4/29/2024
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username FROM Users WHERE username LIKE :username LIMIT 25");
        
        try {
            $stmt->execute([":username" => "%$username%"]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username cannot be empty", "warning");
    }
}
?>

<div class="container-fluid">
    <h1>Associate Teams</h1>
    <form method="POST">
        <div class="row">
            <div class="col">
                <?php render_input(["type" => "search", "name" => "entity_identifier", "placeholder" => "Team Search", "value" => $entityIdentifier]); ?>
            </div>
            <div class="col">
                <?php render_input(["type" => "search", "name" => "username", "placeholder" => "Username Search", "value" => $username]); ?>
            </div>
            <div class="col">
                <?php render_button(["text" => "Search", "type" => "submit"]); // mmt 4/29/2024?>
            </div>
        </div>
    </form>
    <form method="POST">
        <div class="row">
            <div class="col">
                <h3>Teams</h3>
                <table class="table">
                    <?php foreach ($entities as $entity) : ?>
                        <tr>
                            <td>
                                <?php render_input(["type" => "checkbox", "id" => "team_" . se($entity, 'id', "", false), "name" => "entities[]", "label" => se($entity, "name", "", false), "value" => se($entity, 'id', "", false)]); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="col">
                <h3>Users</h3>
                <table class="table">
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td>
                                <?php render_input(["type" => "checkbox", "id" => "user_" . se($user, 'id', "", false), "name" => "users[]", "label" => se($user, "username", "", false), "value" => se($user, 'id', "", false)]); ?>
                            </td>
                        </tr>
                    <?php endforeach; // mmt 4/29/2024 ?>
                </table>
            </div>
        </div>
        <?php render_button(["text" => "Create Associations", "type" => "submit", "color" => "secondary"]); ?>
    </form>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
