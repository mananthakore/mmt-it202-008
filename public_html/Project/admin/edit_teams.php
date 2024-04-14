<?php
require(__DIR__ . "/../../../partials/nav.php");

if(!has_role("Admin")){
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);

if(isset($_POST["name"])){
    foreach($_POST as $k=>$v){
        if(!in_array($k,["name", "nickname", "city", "logo" ] )){
            unset($_POST[$k]);
        }
        $info=$_POST;
    }
    //Insert Data
    $db=getDB();
    $query="UPDATE `NBA_Teams` SET ";

    $params=[];
    foreach($info as $k => $v) {
        if($params){
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of sql injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;   
    }

    $query .= " WHERE id=:id";
    $params[":id"]=$id;

    try{
        $stmt=$db->prepare($query);
        $stmt->execute($params);
        flash("Updated record", "success");
    }
    catch(PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
        flash("Error updating record", "danger");
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
        flash("Error fetching record", "danger");
    }
}
else{
    flash("Invalid ID passed", "danger");
    die(header("Location:" . get_url("admin/list_teams.php")));
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
    <form id = "teamForm" method="POST">
        <?php foreach($form as $field) : ?>
            <?php render_input($field); ?>
        <?php endforeach; ?>
        <?php render_button(["text" => "Update", "type" => "submit"]); ?>
    </form>
</div>

<script>
    function validate(form) {
        const teamInput = form.querySelector("#name");
        const nicknameInput = form.querySelector("#nickname");
        const cityInput = form.querySelector("#city");
        const logoInput = form.querySelector("#logo");

        let isValid = true;

        if (!teamInput.value.trim()) {
            flash("Team is required", "warning");
            isValid = false;
        } 
        if (!nicknameInput.value.trim()) {
            flash("Nickname is required", "warning");
            isValid = false;
        } 

        if (!cityInput.value.trim()) {
            flash("City is required", "warning");
            isValid = false;
        }

        if (!logoInput.value.trim()) {
            flash("Logo is required", "warning");
            isValid = false;
        }
        return isValid;
    }

    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("teamForm");

        form.addEventListener("submit", function (event) {
            if (!validate(form)) {
                event.preventDefault(); // Prevent form submission
            }
        });
    });
</script>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
