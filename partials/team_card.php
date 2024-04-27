<?php
if (!isset($teamData)) {
    error_log("Using Team data partial without data");
    flash("Dev Alert: Team data called without data", "danger");
}
?>

<?php if(isset($teamData)):?>  
<div class="card" style="width: 18rem;">
        <?php if(!empty($teamData["logo"])):?>
            <img src="<?php echo $teamData["logo"];?>" class="card-img-top" alt="Team Logo">
        <?php endif;?>
        <div class="card-body">
            <h5 class="card-title"><?php echo($teamData["name"]);?></h5>
            <p class="card-text">Nickname: <?php safer_echo($teamData["nickname"]);?></p>
            <p class="card-text">City: <?php safer_echo($teamData["city"]);?></p>
            <a href="<?php echo get_url("edit_teams.php?id=" . $id); ?>" class="btn btn-primary">Edit</a>
            <a href="<?php echo get_url("admin/delete_team.php?id=" . $id); ?>" class="btn btn-danger">Delete</a>
        </div>
        <div class="card-body">
            <a href="<?php echo get_url('api/team_details.php?team_id=' . $teamData["id"]); ?>" class="card-link">Favorite Team</a>
        </div>
    </div>
<? endif; ?>