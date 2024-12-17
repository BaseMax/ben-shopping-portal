<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
    <title>Import Coupons</title>

<?php include "_header.php" ?>

<div class="container mt-5">

    <?php if (isset($status) && $status === true): ?>
        <div class="alert alert-success" role="alert">
            File uploaded successfully! <br>
            <strong><?php echo $count_added; ?></strong> coupons were added or updated.
        </div>
    <?php elseif (isset($status) && $status === false): ?>
        <div class="alert alert-danger" role="alert">
            An error occurred: <strong><?php echo htmlspecialchars($error); ?></strong>
        </div>
    <?php endif; ?>

    <h3>Import Coupons</h3>
    <div class="card p-4">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="excelFile" class="form-label">Upload Excel File</label>
                <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xls,.xlsx" required>
            </div>
            <button type="submit" class="btn btn-success">Upload and Import</button>
        </form>
    </div>
</div>

<?php include "_footer.php" ?>
