<?php
// Include navigation and role check logic
require(__DIR__ . "/../../../partials/nav.php");


// Check if the user has admin role, redirect if not
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Fetch team information from form submission
    $name = $_POST["name"] ?? "";
    $nickname = $_POST["nickname"] ?? "";
    $city = $_POST["city"] ?? "";
    $logo = $_POST["logo"] ?? "";


    // Validate input
    if (empty($name) || empty($nickname) || empty($city) || empty($logo)) {
        flash("All fields are required", "danger");
    } else {
        // Insert team information into the database
        $sql = "INSERT INTO NBA_Teams (name, nickname, city, logo) VALUES (:name, :nickname, :city, :logo)";
       $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":nickname", $nickname);
        $stmt->bindValue(":city", $city);
        $stmt->bindValue(":logo", $logo);
        if ($stmt->execute()) {
            flash("Team added successfully", "success");
        } else {
            flash("An error occurred while adding team", "danger");
        }
    }
}
?>


<div class="container-fluid">
    <h3>Add NBA Team</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter team name" required>
        </div>
        <div class="form-group">
            <label for="nickname">Nickname</label>
            <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Enter team nickname" required>
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="Enter team city" required>
        </div>
        <div class="form-group">
            <label for="logo">Logo</label>
            <input type="text" class="form-control" id="logo" name="logo" placeholder="Enter team logo URL" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Team</button>
    </form>
</div>


<?php require(__DIR__ . "/../../../partials/flash.php"); ?>


