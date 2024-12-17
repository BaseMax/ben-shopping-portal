<?php
require "../vendor/autoload.php";

require "../src/database.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

function logged(): bool
{
    if (!isset($_SESSION) || !isset($_SESSION["login"]) || !isset($_SESSION["login_id"]) || $_SESSION["login"] !== true) return false;

    return true;
}

function addNewCoupon(PDO &$db, string $serial, string $fullname, bool $used): bool
{
    $sql = "INSERT INTO `coupons` (`serial`, `fullname`, `used`, `created_time`) VALUES (?, ?, ?, ?);";
    $stmt = $db->prepare($sql);

    return $stmt->execute([$serial, $fullname, $used ? 1 : 0, time()]);
}

function updateCouponName(PDO &$db, string $serial, string $fullname): bool
{
    $sql = "UPDATE `coupons` SET `fullname` = ?, `last_update_time` = ? WHERE `serial` = ?;";
    $stmt = $db->prepare($sql);

    return $stmt->execute([$fullname, time(), $serial]);
}

function addCouponIfIsNew(PDO &$db, string $serial, string $fullname, bool $used = false): bool
{
    $sql = "SELECT * FROM `coupons` WHERE `serial` = ?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$serial]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon === false) {
        return addNewCoupon($db, $serial, $fullname, $used);

    } else {
        if ($fullname === $coupon["fullname"]) return false;

        return updateCouponName($db, $serial, $fullname);
    }
}

function getCurrentAdmin(PDO &$db): ?array
{
    if (!logged()) return null;

    $admin_id = $_SESSION["login_id"];

    $sql = "SELECT * FROM `admins` WHERE `id` = ?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    return $admin;
}

function changeAdminUsernameById(PDO &$db, int $admin_id, string $new_username): bool
{
    $sql = "UPDATE `admins` SET `username` = ? WHERE `admins`.`id` = ?;";
    $stmt = $db->prepare($sql);
    
    return $stmt->execute([$new_username, $admin_id]);
}

function changeAdminPasswordById(PDO &$db, int $admin_id, string $new_password): bool
{
    $sql = "UPDATE `admins` SET `password` = ? WHERE `admins`.`id` = ?;";
    $stmt = $db->prepare($sql);

    $new_password = md5($new_password);
    
    return $stmt->execute([$new_password, $admin_id]);
}

session_start();

Flight::set("flight.views.path", "../view/");

Flight::register("db", "Database", ["getConnection"]);

Flight::route("GET /", function() {
    $db = Flight::db()->getConnection();

    if (logged() === false) {
        Flight::redirect("/login");
        return;
    }

    $sql = "SELECT * FROM `coupons` ORDER BY `id` DESC;";
    $stmt = $db->prepare($sql);
    $stmt->execute([]);
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $params = [];
    $params["count"] = count($coupons);
    $params["items"] = $coupons;

    Flight::render("panel", $params);
});

Flight::route("GET /login", function() {
    $db = Flight::db()->getConnection();

    Flight::render("login", []);
});

Flight::route("GET /import-coupons", function() {
    $db = Flight::db()->getConnection();

    $params = [];

    Flight::render("import_coupons", $params);
});

Flight::route("POST /import-coupons", function() {
    $db = Flight::db()->getConnection();

    $admin = getCurrentAdmin($db);

    $params = [];

    $upload_dir = "../upload/";

    if (! file_exists($upload_dir)) mkdir($upload_dir);

    $file_object = $_FILES["excelFile"];
    $upload_file = $upload_dir . $admin["id"] . "_" . rand(1000000, 9999999) . "_" . $file_object["name"];

    $moved = move_uploaded_file($file_object["tmp_name"], $upload_file);

    if (! $moved) {
        $params["status"] = false;

        $upload_error = $file_object["error"];
        $error_message = match ($upload_error) {
            UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
            UPLOAD_ERR_PARTIAL    => "The uploaded file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE    => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder on the server.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write the file to disk.",
            UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload.",
            default               => "Unknown error occurred during file upload.",
        };

        $params["status"] = false;
        $params["error"] = "File upload failed. Reason: " . $error_message . " (Error Code: {$upload_error}).";
    } else {
        try {
            $count_added = 0;

            $spreadsheet = IOFactory::load($upload_file);

            $worksheet = $spreadsheet->getActiveSheet();
            $row_index = 0;
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowValue = $cell->getValue();
                    $rowData[] = $rowValue;
                }

                if ($row_index !== 0) {
                    $added = addCouponIfIsNew($db, $rowData[0], $rowData[1]);

                    if ($added) $count_added++;
                }

                $row_index++;
            }
            $params["status"] = true;
            $params["count_added"] = $count_added;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $params["status"] = false;
            $params["error"] =  "Error loading file: " . $e->getMessage();
        }
    }

    if ($upload_file && file_exists($upload_file)) {
        unlink($upload_file);
    }

    Flight::render("import_coupons", $params);
});

Flight::route("GET /delete-coupon/@id", function($id) {
    $db = Flight::db()->getConnection();

    $sql = "DELETE FROM `coupons` WHERE `id` = ?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);

    Flight::redirect("/");
});

Flight::route("GET /update-coupon/@id", function($id) {
    $db = Flight::db()->getConnection();

    $sql = "SELECT * FROM `coupons` WHERE `id` = ?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$coupon) {
        Flight::redirect("/");
        return;
    }

    Flight::render("update_coupon", ["coupon" => $coupon]);
});

Flight::route("POST /update-coupon/@id", function($id) {
    $db = Flight::db()->getConnection();

    $serial = $_POST['serial'];
    $fullname = $_POST['fullname'];
    $used = isset($_POST['used']) ? 1 : 0;

    $sql = "UPDATE `coupons` SET `serial` = ?, `fullname` = ?, `used` = ? WHERE `id` = ?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$serial, $fullname, $used, $id]);

    Flight::redirect("/");
});

Flight::route("GET /profile", function() {
    $db = Flight::db()->getConnection();

    $admin = getCurrentAdmin($db);

    $params = [];
    $params["admin"] = $admin;

    Flight::render("update_profile", $params);
});

Flight::route("POST /profile", function() {
    $db = Flight::db()->getConnection();
    
    $admin = getCurrentAdmin($db);

    $new_username = $_POST["username"];
    $new_password = $_POST["password"];

    $username_changed = false;
    $username_changed_ok = false;
    $password_changed = false;
    $password_changed_ok = false;

    if ($new_username !== $admin["username"]) {
        $username_changed = true;
        $username_changed_ok = changeAdminUsernameById($db, $admin["id"], $new_username);
    }

    if ($new_password !== "") {
        $password_changed = true;
        $password_changed_ok = changeAdminPasswordById($db, $admin["id"], $new_password);
    }

    if ($username_changed_ok || $password_changed_ok) {
        $admin = getCurrentAdmin($db);
    }

    $params = [];
    $params["admin"] = $admin;
    $params["username_changed"] = $username_changed;
    $params["username_changed_ok"] = $username_changed_ok;
    $params["password_changed"] = $password_changed;
    $params["password_changed_ok"] = $password_changed_ok;

    Flight::render("update_profile", $params);
});

Flight::route("POST /login", function() {
    $db = Flight::db()->getConnection();

    $username = $_POST["username"];
    $password = md5($_POST["password"]);

    $sql = "SELECT * FROM `admins` WHERE `username` = ? AND `password` = ?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin === false) {
        Flight::render("login", []);
    } else {
        $_SESSION["login_id"] = $admin["id"];
        $_SESSION["login"] = true; // TODO: make it more secure
        Flight::redirect("/");        
    }
});

Flight::route("GET /health", function() {
    Flight::json(["status" => "Service is running fine!"]);
});

Flight::route("/logout", function() {
    session_destroy();
    Flight::redirect("/");
});

Flight::start();
