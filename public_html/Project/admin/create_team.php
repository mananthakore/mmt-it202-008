<?php
// Include navigation and role check logic
require(__DIR__ . "/../../../partials/nav.php");

// Check if the user has admin role, redirect if not
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

// Initialize an array to store validation errors
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    // Fetch team information from form submission
    $name = $_POST["name"] ?? "";
    $nickname = $_POST["nickname"] ?? "";
    $city = $_POST["city"] ?? "";
    $logo = $_POST["logo"] ?? "";

    // Validate input
    if (empty($name)) {
        $errors['name'] = "Team name is required";
    }

    if (empty($nickname)) {
        $errors['nickname'] = "Team nickname is required";
    }

    if (empty($city)) {
        $errors['city'] = "Team city is required";
    }

    if (empty($logo)) {
        $errors['logo'] = "Team logo URL is required";
    }

    // If there are no errors and the form was submitted, proceed to insert into the database
    if (empty($errors)) {
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
            // Redirect to a different page after successful submission
            header("Location: $BASE_PATH" . "/home.php");
            exit; // Make sure to exit after redirection
        } else {
            flash("An error occurred while adding team", "danger");
        }
    }
}
?>


<div class="container-fluid">
    <h3>Add NBA Team</h3>
    <form id="teamForm" method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control <?php if (isset($errors['name'])) echo 'is-invalid'; ?>" id="name" name="name" placeholder="Enter team name" required>
            <?php if (isset($errors['name'])) : ?>
                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="nickname">Nickname</label>
            <input type="text" class="form-control <?php if (isset($errors['nickname'])) echo 'is-invalid'; ?>" id="nickname" name="nickname" placeholder="Enter team nickname" required>
            <?php if (isset($errors['nickname'])) : ?>
                <div class="invalid-feedback"><?php echo $errors['nickname']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control <?php if (isset($errors['city'])) echo 'is-invalid'; ?>" id="city" name="city" placeholder="Enter team city" required>
            <?php if (isset($errors['city'])) : ?>
                <div class="invalid-feedback"><?php echo $errors['city']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="logo">Logo</label>
            <input type="text" class="form-control <?php if (isset($errors['logo'])) echo 'is-invalid'; ?>" id="logo" name="logo" placeholder="Enter team logo URL" required>
            <?php if (isset($errors['logo'])) : ?>
                <div class="invalid-feedback"><?php echo $errors['logo']; ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Add Team</button>
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
            flash("[js]Team name is required", "warning");
            isValid = false;
        }
        if (!nicknameInput.value.trim()) {
            flash("[js]Team nickname is required", "warning");
            isValid = false;
        }

        if (!cityInput.value.trim()) {
            flash("[js]Team city is required", "warning");
            isValid = false;
        }

        if (!logoInput.value.trim()) {
            flash("[js]Team logo URL is required", "warning");
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
<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
