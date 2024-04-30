<?php

require(__DIR__ . "/../../partials/nav.php"); // mmt 4/17/2024


$id = se($_GET, "team_id", -1, false);
if ($id == -1) {
    // If team_id is not set, try to get it as "id"
    $id = se($_GET, "id", -1, false);
}

$teamData=[];
if($id>-1){
    $db=getDB();
    $query="SELECT name, nickname, city, logo FROM `NBA_Teams` WHERE id=:id";
    try{
        $stmt=$db->prepare($query);
        $stmt->execute([":id"=>$id]);
        $r=$stmt->fetch();
        if($r) { 
            $teamData = $r;
        }
    }
    catch(PDOException $e){
        error_log("Error fetching team data: " . var_export($e, true));
        flash("Error fetching team data", "danger");
    }
}
else{
    flash("Invalid id passed", "danger");
    redirect("teams.php"); // mmt 4/17/2024
}

foreach($teamData as $key=>$value) { 
    if(is_null($value)) { 
        $teamData[$key] = "N/A";
    }
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
           <!-- <a href=" <//?php echo get_url("edit_teams.php?id=" . $id); ?>" class="btn btn-primary">Edit</a> -->
           <!-- <a href=" <//?php echo get_url("admin/delete_team.php?id=" . $id); ?>" class="btn btn-danger">Delete</a> -->
           <div class = "card-body">
                <a href="<?php echo get_url('api/team_details.php?team_id=' . $id); ?>" class="card-link">Favorite Team</a>
            </div>
        </div>
    </div>
</div>

<?php require(__DIR__ . "/../../partials/flash.php");?>