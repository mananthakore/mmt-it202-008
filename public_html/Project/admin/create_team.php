<?php
// Include navigation and role check logic, mmt 4/17/2024
require(__DIR__ . "/../../../partials/nav.php");

// Check if the user has admin role, redirect if not
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

// Initialize an array to store validation errors

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = se($_POST, "name", "", false);
    $nickname = se($_POST, "nickname", "", false);
    $city = se($_POST, "city", "", false);
    $logo = se($_POST, "logo", "", false);
    
    $error = false;
    // Fetch team information from form submission

    // Validate input
    if (empty($name)) {
        flash("[server] Team name is required", "warning");
        $error = true;
    }

    if (empty($nickname)) { // mmt 4/17/2024
        flash("[server] Team nickname is required", "warning");
        $error = true;
    }

    if (empty($city)) {
        flash("[server] Team city is required", "warning");
        $error = true;
    }

    if (empty($logo)) {
        flash("[server] Team logo URL is required", "warning");
        $error = true;
    }

    // If there are no errors and the form was submitted, proceed to insert into the database
    if(!$error) { 
        $db = getDB();
        $query = "INSERT INTO NBA_Teams (name, nickname, city, logo) VALUES (:name, :nickname, :city, :logo)";
        try { 
            $stmt = $db->prepare($query);
            $stmt->execute([
                ":name" => $name, 
                ":nickname" => $nickname, 
                ":city" => $city, 
                ":logo" => $logo
        ]);
        flash("Team added successfuly", "success"); 
    } catch(PDOException $e) { 
        if($e->errorInfo[1] === 1062) { 
            flash("A team with this info already exists", "warning");
        }
        else { 
            flash("An unexpected error occurred", "danger");
            error_log("Database error: " . $e->getMessage()); // mmt 4/17/2024
        }
    }       

    }
}

?>

<div class="container-fluid">
    <h3>Add NBA Team</h3>
    <form method = "POST" onsubmit = "return validate(this);" >
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control <?php if (isset($error['name'])) echo 'is-invalid'; ?>" id="name" name="name" placeholder="Enter team name" required>
            <?php if (isset($error['name'])) : ?>
                <div class="invalid-feedback"><?php echo $error['name']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="nickname">Nickname</label> <!-- mmt 4/17/2024 -->
            <input type="text" class="form-control <?php if (isset($error['nickname'])) echo 'is-invalid'; ?>" id="nickname" name="nickname" placeholder="Enter team nickname" required>
            <?php if (isset($error['nickname'])) : ?>
                <div class="invalid-feedback"><?php echo $error['nickname']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control <?php if (isset($error['city'])) echo 'is-invalid'; ?>" id="city" name="city" placeholder="Enter team city" required>
            <?php if (isset($error['city'])) : ?>
                <div class="invalid-feedback"><?php echo $error['city']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="logo">Logo</label>
            <input type="text" class="form-control <?php if (isset($error['logo'])) echo 'is-invalid'; ?>" id="logo" name="logo" placeholder="Enter team logo URL" required>
            <?php if (isset($error['logo'])) : ?>
                <div class="invalid-feedback"><?php echo $error['logo']; ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Add Team</button>
    </form>
</div>
<script>
    function validate(form) { // mmt 4/17/2024
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

        if (!cityInput) {
            flash("[js] Team city is required", "warning");
            isValid = false;
        }

        if (!logoInput) {
            flash("[js] Team logo URL is required", "warning");
            isValid = false;
        }
        return isValid; // mmt 4/17/2024
    }
</script>
<?php require(__DIR__ . "/../../../partials/flash.php"); ?>