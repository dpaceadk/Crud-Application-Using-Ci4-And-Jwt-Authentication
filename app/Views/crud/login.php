<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($validation)): ?>
        <div>
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div>
            <?= $error ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?= base_url('login') ?>">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
