<?php
// Include navigation and role check logic
require(__DIR__ . "/../../../partials/nav.php");

// Check if the user has admin role, redirect if not
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
if(isset($_POST["action"])){
    $action=$_POST["action"];
    $name= se($_POST, "name", "", false);
    $info=[];
    if($name){
        if($action==="fetch"){
            $result=fetch_team($name);
            error_log("Data from API" . var_export($result, true));
            if($result){
                $info=$result;
                $info["is_api"] = 1;
            }

        }
        else{
            flash("You must provide a name", "warning");
        }
    }
    
    //Insert Data
    $db=getDB();
    $query="INSERT INTO `NBA_Teams` ";
    $columns=[];
    $params=[];
    foreach($info as $k => $v) {
            array_push($columns, "$k");
            $params[":$k"] = $v;   
    }


    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",",array_keys($params)) . ")";
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));



    try{
        $stmt=$db->prepare($query);
        $stmt->execute($params);
        flash("Fetched record " . $db->lastInsertId(), "success");
    }
    catch(PDOException $e){
        if($e->errorInfo[1] === 1062){
            flash("Team already exists, please enter a different team", "warning");
        }
        else{
            error_log("Something broke with the query" . var_export($e, true));
            flash("An error occured", "danger");
        }
    }
}

//TODO handle manual create driver

?>
<div class="container-fluid">
    <h3>Fetch Team</h3>
    <div id="fetch" class="tab-target">
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "name", "placeholder" => "Team name", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
        <?php render_button(["text" => "Search", "type" => "submit",]); ?>
    </form>
    </div>

</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>