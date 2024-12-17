<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
    <title>Edit Coupon</title>

<?php include "_header.php"; ?>

<div class="container mt-5">
    <h3>Edit Coupon</h3>
    <div class="card p-4">
        <form action="/update-coupon/<?php echo htmlspecialchars($coupon['id']); ?>" method="POST">
            <div class="mb-3">
                <label for="serial" class="form-label">Serial</label>
                <input type="text" class="form-control" id="serial" name="serial" value="<?php echo htmlspecialchars($coupon['serial']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($coupon['fullname']); ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="usedFlag" name="used" <?php echo $coupon['used'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="usedFlag">Mark as Used</label>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
        <a href="/delete-coupon/<?php echo htmlspecialchars($coupon['id']); ?>" class="btn btn-danger mt-2" onclick="return confirm('Are you sure you want to delete this coupon?');">Delete Coupon</a>
    </div>
</div>

<?php include "_footer.php"; ?>
