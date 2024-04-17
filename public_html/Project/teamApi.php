<?php
require(__DIR__ . "/../../partials/nav.php");


$result = [];
if (isset($_GET["teamName"])) {
    $data = ["name" => $_GET["teamName"]];
    $endpoint = "https://api-nba-v1.p.rapidapi.com/teams";
    $isRapidAPI = true;
    $rapidAPIHost = "api-nba-v1.p.rapidapi.com";
    $result = get($endpoint, "TEAMS_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }

    if(isset($result["response"])){
        $result=$result["response"];

    }
    $db=getDB();
    $query="INSERT INTO `NBA_Teams` ";
    $columns=[];
    $params=[];

    foreach($result as $index => $row) {
        foreach ($row as $k => $v) {
            if($index === 0){
            array_push($columns, "$k");
            }
            if($k === "code") { 
                continue;
            }
            if ($k === "allStar") { 
                continue;
            }
            if ($k === "nbaFranchise") { 
                continue;
            }
            if ($k === "leagues") { 
                continue;
            }
            $params[":$k$index"] = $v;
        }
    }
    

    unset($columns[3]);
    unset($columns[7]);
    unset($columns[8]);
    unset($columns[6]);


    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",",array_keys($params)) . ")";
    var_export($query);
    error_log(var_export($params, true));



    try{
        $stmt=$db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record", "success");
    }
    catch(PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
    }
}
?>

<div class="container-fluid">
    <h1>NBA Team Info</h1>
    <p>Enter the team's name to fetch its information.</p>
    <form>
        <div>
            <label>Team Name</label>
            <input name="teamName" />
            <input type="submit" value="Fetch Team Info" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $team) : ?>
                <pre>
                    <?php var_export($team);?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>


