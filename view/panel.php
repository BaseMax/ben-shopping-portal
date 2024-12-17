<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
    <title>Admin Dashboard</title>

<?php include "_header.php" ?>

<div class="container mt-4">
    <h2 class="mb-4">Coupon Management (<?= number_format($count) ?>)</h2>
    <a href="/import-coupons" class="btn btn-success mb-3">Import Coupons</a>

    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by Serial, Full Name, or Status">
    </div>

    <div class="mb-3">
        <select id="statusFilter" class="form-control">
            <option value="all">All Statuses</option>
            <option value="used">Only Used</option>
            <option value="unused">Only Unused</option>
        </select>
    </div>

    <table class="table table-bordered table-hover" id="couponTable">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Serial</th>
            <th>Full Name</th>
            <th>Used</th>
            <th>Created time</th>
            <th>Last update time</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $index => $item) { ?>
            <?php
            if ($item['used'] == 0) {
                $badgeColor = 'bg-success';
                $badgeText = 'Not Used';
            } elseif ($item['used'] == 1) {
                $badgeColor = 'bg-danger';
                $badgeText = 'Used';
            } else {
                $badgeColor = 'bg-secondary';
                $badgeText = 'Expired';
            }
            ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($item['serial']) ?></td>
            <td><?= htmlspecialchars($item['fullname']) ?></td>
            <td><span class="badge <?= $badgeColor ?>"><?= $badgeText ?></span></td>
            <td><?= date('Y-m-d H:i:s', $item['created_time']) ?></td>
            <td><?= $item['last_update_time'] ? date('Y-m-d H:i:s', $item['last_update_time']) : 'Not updated' ?></td>
            <td>
                <a href="/update-coupon/<?= $item["id"] ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="/delete-coupon/<?= $item["id"] ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this coupon?');">
                   Delete
                </a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const statusFilter = document.getElementById("statusFilter");
    const tableRows = document.querySelectorAll("#couponTable tbody tr");

    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const selectedFilter = statusFilter.value;

        tableRows.forEach(row => {
            const serialText = row.cells[1].textContent.toLowerCase();
            const fullnameText = row.cells[2].textContent.toLowerCase();
            const statusText = row.cells[3].textContent.toLowerCase();

            let matchesSearch = serialText.includes(searchValue) || fullnameText.includes(searchValue);

            let matchesFilter = false;
            if (selectedFilter === "all") {
                matchesFilter = true;
            } else if (selectedFilter === "used") {
                matchesFilter = statusText.includes("used") && !statusText.includes("not used");
            } else if (selectedFilter === "unused") {
                matchesFilter = statusText.includes("not used");
            }

            if (matchesSearch && matchesFilter) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    searchInput.addEventListener("input", filterTable);
    statusFilter.addEventListener("change", filterTable);
});
</script>

<?php include "_footer.php" ?>
