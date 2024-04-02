<?php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
?>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" required maxlength="30" />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        var Email = form.email.value;
        var Username = form.username.value;
        var Password = form.password.value;
        var Confirm = form.confirm.value;
        var hasError = false;

        function is_valid_username(Username) {
            const re =/^[a-z0-9_-]{3,16}$/;
            return re.test(Username);
        }
        
        function is_valid_email(Email) {
            const re = /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})*$/;
            return re.test(Email);
        }

        if (Email === "") {
            flash("[client] Email cannot be empty", "caution");
            hasError = true;
        }
        else if (!is_valid_email(Email)) {
                flash("[client] Email is invalid", "caution");
                hasError = true;
            }
        if (Username === "") {
            flash("[client] Username cannot be empty", "caution");
            hasError = true;
        }
        else if (!is_valid_username(Username)) {
                flash("[client] Username is invalid ", "caution");
                hasError = true;
            }
        if (Password === "") {
            flash("[client] Password cannot be empty", "caution");
            hasError = true;
        }
        else if (Password.length < 8) {
                flash("[client] Password is too short", "caution");
                hasError = true;
        }
        if (Password !== Confirm) {
            flash("[client] Passwords do not match", "caution");
            hasError = true;
        }

        return !hasError;
        //mmt48 4/1/2024
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se(
        $_POST,
        "confirm",
        "",
        false
    );
    $username = se($_POST, "username", "", false);
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!preg_match('/^[a-z0-9_-]{3,16}$/', $username)) {
        flash("Username must only contain 3-30 characters a-z, 0-9, _, or -", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (strlen($password) < 8) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>