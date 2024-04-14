<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

// Build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Team Name", "label" => "Team Name", "include_margin" => false],
    ["type" => "text", "name" => "city", "placeholder" => "City", "label" => "City", "include_margin" => false],
    ["type" => "text", "name" => "nickname", "placeholder" => "Nickname", "label" => "Nickname", "include_margin" => false],
    ["type" => "text", "name" => "logo", "placeholder" => "Logo (Link)", "label" => "Logo (Link)", "include_margin" => false],
];

$query = "SELECT id, name, city, nickname, logo FROM `NBA_Teams` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    die(header("Location: " . $session_key));
} else {
    $session_data = session_load($session_key);
}

if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) {
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
    if (!empty($nickname)) {
        $query .= " AND nickname LIKE :nickname";
        $params[":nickname"] = "%$nickname%";
    }

    // Filter by logo (link)
    $logo = se($_GET, "logo", "", false);
    if (!empty($logo)) {
        $query .= " AND logo LIKE :logo";
        $params[":logo"] = "%$logo%";
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

$table = [
    "data" => $results,
    "title" => "NBA Teams",
    "ignored_columns" => ["id"],
    // Add edit and delete URLs if needed
    // "edit_url" => get_url("admin/edit_team.php"),
    // "delete_url" => get_url("admin/delete_team.php"),
];
?>
<div class="container-fluid">
    <h3>List NBA Teams</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">
            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_table($table); ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
