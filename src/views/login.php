<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="static/styles/main.css">
</head>
<body>
<div class="registration-container">

<form method="post">
    Enter your credentials:<br/>

    <label>
        <span>Username:</span>
        <input type="text" name="user" required/><br/>
    </label>
    <label>
        <span>Password:</span>
        <input type="password" name="pass" required/><br/>
    </label>
    <div>
        <a href="gallery" class="cancel">Cancel</a>
        <span class="pad3"><input class="button" type="submit" value="Confirm"/></span>
    </div>
</form>
</div>
</body>
</html>
