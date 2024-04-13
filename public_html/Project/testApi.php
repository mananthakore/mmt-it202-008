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
        $response = json_decode($result["response"], true);
       
        // Check if the response contains the expected key
        if (isset($response['api']) && isset($response['api']['teams'])) {
            $teams = $response['api']['teams'];
           
            // Insert retrieved team information into the database
            if (!empty($teams)) {
                foreach ($teams as $team) {
                    $name = $team['full_name'] ?? '';
                    $nickname = $team['nickname'] ?? '';
                    $city = $team['city'] ?? '';
                    $logo = $team['logo'] ?? '';
                   
                    // Insert into database
                    $sql = "INSERT INTO NBA_Teams (name, nickname, city, logo) VALUES ('$name', '$nickname', '$city', '$logo')";
                    $insert_result = mysqli_query($conn, $sql);
                    if (!$insert_result) {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    }
                }
            }
        }
    } else {
        $result = [];
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
