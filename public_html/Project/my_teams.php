<?php
require(__DIR__ . "/../../partials/nav.php"); // mmt 4/17/2024
$db = getDB();
//remove all associations
if (isset($_GET["remove"])) {
    $query = "DELETE FROM `UserTeams` WHERE user_id = :user_id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id" => get_user_id()]);
        flash("Successfully removed all teams", "success");
    } catch (PDOException $e) {
        error_log("Error removing team associations: " . var_export($e, true));
        flash("Error removing team associations", "danger");
    }

    redirect("my_teams.php");
}

$db = getDB();
if (isset($_GET["remove"])) {
    $query = "DELETE FROM `UserTeams`";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        flash("All teams removed", "success");
    } catch (PDOException $e) {
        error_log("Error removing all teams: " . var_export($e, true));
        flash("Error removing all teams", "danger");
    }
    redirect("my_teams.php");
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["removeTeamId"])) {
    $removeTeamId = $_POST["removeTeamId"];
    $query = "DELETE FROM `UserTeams` WHERE team_id = :team_id";
    $params = [":team_id" => $removeTeamId];
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Team selected removed successfully", "success");
        redirect("admin/team_associations.php");
    } catch (PDOException $e) {
        error_log("Error removing team: " . $e->getMessage());
        flash("Error removing team", "danger");
    }
}

// Build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Team Name", "label" => "Team Name", "include_margin" => false],
    ["type" => "text", "name" => "city", "placeholder" => "City", "label" => "City", "include_margin" => false],
    ["type" => "text", "name" => "nickname", "placeholder" => "Nickname", "label" => "Nickname", "include_margin" => false],
    ["type" => "text", "name" => "logo", "placeholder" => "Logo (Link)", "label" => "Logo (Link)", "include_margin" => false],
    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false]
];

$total_records = get_total_count("`NBA_Teams` t
JOIN `UserTeams` ut ON t.id = ut.team_id
WHERE ut.user_id = :user_id", [":user_id" => get_user_id()]);

$query = "SELECT t.id AS team_id, name, city, nickname, logo, user_id FROM `NBA_Teams` t
JOIN `UserTeams` utt ON t.id = utt.team_id 
WHERE utt.user_id = :user_id";

$params = [":user_id" => get_user_id()];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    redirect($session_key);
} else {
    $session_data = session_load($session_key);
}

if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) { // mmt 4/17/2024
    if ($session_data) {
        $_GET = $session_data;
    }
}

if (count($_GET) > 0) {
    session_save($session_key, $_GET);
    $keys = array_keys($_GET);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }
    // Filter by team name
    $name = se($_GET, "name", "", false);
    if (!empty($name)) {
        $query .= " AND name LIKE :name";
        $params[":name"] = "%$name%";
    }

    // Filter by city
    $city = se($_GET, "city", "", false);
    if (!empty($city)) {
        $query .= " AND city LIKE :city";
        $params[":city"] = "%$city%";
    }

    // Filter by nickname
    $nickname = se($_GET, "nickname", "", false);
    if (!empty($nickname)) { // mmt 4/17/2024
        $query .= " AND nickname LIKE :nickname";
        $params[":nickname"] = "%$nickname%";
    }

    // Filter by logo (link)
    $logo = se($_GET, "logo", "", false);
    if (!empty($logo)) {
        $query .= " AND logo LIKE :logo";
        $params[":logo"] = "%$logo%";
    }
    try {
        $limit = (int)se($_GET, "limit", "10", false);
    } catch (Exception $e) {
        $limit = 10;
    }
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
}

$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching NBA teams: " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

foreach ($results as $index => $teamData) {
    foreach ($teamData as $key => $value) {
        if (is_null($value)) {
            $results[$index][$key] = "N/A";
        }
    }
}

$table = [
    "data" => $results,
    "title" => "NBA Teams",
    "ignored_columns" => ["team_id"],["user_id"], // mmt 4/17/2024
    // Add edit and delete URLs if needed
   // "edit_url" => get_url("edit_teams.php"),
   // "delete_url" => get_url("delete_teams.php"),
    "view_url" => get_url("team.php"),
    "removeButton" => true,
    "primary_key" =>"team_id"
];
?>
<div class="container-fluid">
    <h3>My Favorite Teams</h3>
    <div>
        <a href="?remove" onclick="confirm('Are you sure')?'':event.preventDefault()" class="btn btn-danger">Remove All Teams</a>
    </div>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">
            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <?php render_result_counts(count($results), $total_records); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
        <?php if (isset($userData["username"])) : ?>
            <div class="card-header">
                Owned By: <?php se($userData, "username", "N/A"); ?>
            </div>
        <?php endif; ?>
    </form> <!-- mmt 4/17/2024 -->
    <?php render_table($table); ?>
    <div class = "row">
    <?php foreach($results as $teamData):?>
        <div class = "col"></div>
        <?php endforeach;?>
</div>

<?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
