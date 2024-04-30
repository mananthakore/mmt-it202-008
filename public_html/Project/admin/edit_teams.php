<?php
require(__DIR__ . "/../../../partials/nav.php");
// mmt 4/17/2024
if(!has_role("Admin")){
    flash("You do not have permission to view this page", "warning");
    redirect("home.php");
}

$id = se($_GET, "id", -1, false);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = se($_POST, "name", "", false);
    $nickname = se($_POST, "nickname", "", false);
    $city = se($_POST, "city", "", false);
    $logo = se($_POST, "logo", "", false);

    $errors = false;

    // Server-side validation
    if (empty($name)) {
        flash("[server] Team is required", "warning");
        $errors = true;
    } 

    if (empty($nickname)) {
        flash("[server] Nickname is required", "warning"); // mmt 4/17/2024
        $errors = true;
    } 

    if (empty($city)) {
        flash("[server] City is required", "warning");
        $errors = true;
    }

    if (empty($logo)) {
        flash("[server] Logo URL is required", "warning");
        $errors = true;
    }
    
    if (!$errors) {

        $db = getDB();
        $query = "UPDATE `NBA_Teams` SET ";
        $params = [];

        foreach ($_POST as $k => $v) {
            if ($params) {
                $query .= ",";
            }
            $query .= "$k=:$k";
            $params[":$k"] = $v;
        }

        $query .= " WHERE id=:id";
        $params[":id"] = $id;

        try { // mmt 4/17/2024
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            flash("Updated record", "success");
        } 
        catch (PDOException $e) {
            error_log("Something broke with the query" . var_export($e, true));
        }
    }
}


$team = [];
if($id > -1){
    // Fetch team data
    $db = getDB();
    $query = "SELECT name, nickname, city, logo FROM `NBA_Teams` WHERE id=:id";

    try{
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $team = $stmt->fetch();
    }
    catch(PDOException $e){
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger"); // mmt 4/17/2024
    }
}
else{
    flash("Invalid ID passed", "danger");
    redirect("admin/list_teams.php");
}

if($team){
    $form = [
        ["type" => "text", "name" => "name", "placeholder" => "Team Name", "label" => "Team Name", "value" => $team["name"], "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "nickname", "placeholder" => "Team Nickname", "label" => "Team Nickname", "value" => $team["nickname"], "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "city", "placeholder" => "City", "label" => "City", "value" => $team["city"], "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "logo", "placeholder" => "Logo URL", "label" => "Logo URL", "value" => $team["logo"], "rules" => ["required" => "required"]]
    ];
}

?>

<div class="container-fluid">
    <h3>Edit Team</h3>
    <div>
        <a href="<?php echo get_url("admin/list_teams.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method = "POST" onsubmit = "return validate(this);">
        <?php foreach($form as $field) : ?>
            <?php render_input($field); ?>
        <?php endforeach;  // mmt 4/17/2024?> 
        <?php render_button(["text" => "Update", "type" => "submit"]); ?> 
    </form>
</div>

<script>
    function validate(form) {
        const teamInput = form.name.value;
        const nicknameInput = form.nickname.value;
        const cityInput = form.city.value;
        const logoInput = form.logo.value;

        let isValid = true;

        if (!teamInput) {
            flash("[js] Team name is required", "warning");
            isValid = false;
        }
        if (!nicknameInput) {
            flash("[js] Team nickname is required", "warning");
            isValid = false;
        }

        if (!cityInput) { // mmt 4/17/2024
            flash("[js] Team city is required", "warning");
            isValid = false;
        }

        if (!logoInput) {
            flash("[js] Team logo URL is required", "warning");
            isValid = false;
        }
        return isValid;
    }
</script>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
