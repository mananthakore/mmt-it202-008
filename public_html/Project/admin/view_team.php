<?php

require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);

$teamData=[];
if($id>-1){
    $db=getDB();
    $query="SELECT name, nickname, city, logo FROM `NBA_Teams` WHERE id=:id";
    try{
        $stmt=$db->prepare($query);
        $stmt->execute([":id"=>$id]);
        $teamData=$stmt->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        error_log("Error fetching team data: " . var_export($e, true));
        flash("Error fetching team data", "danger");
    }
}
else{
    flash("Invalid id passed", "danger");
    die(header("Location: " . get_url("admin/list_teams.php")));
}
?>

<div class="container mt-4 d-flex justify-content-center">
    <div class="card" style="width: 18rem;">
        <?php if(!empty($teamData["logo"])):?>
            <img src="<?php echo $teamData["logo"];?>" class="card-img-top" alt="Team Logo">
        <?php endif;?>

        <div class="card-body">
            <h5 class="card-title"><?php echo($teamData["name"]);?></h5>
            <p class="card-text">Nickname: <?php safer_echo($teamData["nickname"]);?></p>
            <p class="card-text">City: <?php safer_echo($teamData["city"]);?></p>
        </div>
    </div>
</div>

<?php require(__DIR__ . "/../../../partials/flash.php");?>