<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
    <title>Edit Profile</title>

<?php include "_header.php" ?>

<?php if (isset($password_changed) && $password_changed) { ?>
    <meta http-equiv="refresh" content="3;url=/logout">
<?php } ?>

<div class="container mt-5">
    <?php if (isset($username_changed) && $username_changed && isset($password_changed) && $password_changed) { ?>
        <div class="alert alert-success" role="alert">
            Username and password have been updated successfully!
        </div>
    <?php } else if (isset($username_changed) && $username_changed) { ?>
        <div class="alert alert-success" role="alert">
            Username has been updated successfully!
        </div>
    <?php } else if (isset($password_changed) && $password_changed) { ?>
        <div class="alert alert-success" role="alert">
            Password has been updated successfully!
        </div>
    <?php } ?>

    <h3>Edit Profile</h3>
    <div class="card p-4">
        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $admin["username"] ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank if unchanged" autocomplete="false">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>

<?php include "_footer.php" ?>
