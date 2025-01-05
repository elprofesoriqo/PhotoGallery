<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="static/styles/main.css">
</head>
<body>
<div class="registration-container">
    <h2 class="registration-title">Registration</h2>

    <form method="post" class="registration-form">
        <p class="form-text">Please enter your registration details:</p>

        <div class="form-group">
            <label for="login">Username:</label>
            <input type="text" id="login" name="login" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="pass">Password:</label>
            <input type="password" id="pass" name="pass" required>
        </div>

        <div class="form-group">
            <label for="pass2">Confirm Password:</label>
            <input type="password" id="pass2" name="pass2" required>
        </div>

        <div class="form-actions">
            <a href="gallery" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Submit</button>
        </div>
    </form>
</div>
</body>
</html>
