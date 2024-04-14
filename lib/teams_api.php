<?php 
function fetch_team($search) { 
    $data = ["search" => $search];
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
    $query = [];
    foreach($result as $index => $row) {
        foreach ($row as $k => $v) {
            if($index === 0){
            array_push($result, "$k");
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
            $query["$k"] = $v;
        }
    }
    return $query;
}