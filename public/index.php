<?php
require '../vendor/autoload.php';
require '../src/database.php';

session_start();

Flight::set('flight.views.path', '../view/');

Flight::register('db', 'Database', ['getConnection']);

Flight::route('GET /', function() {
    $db = Flight::db()->getConnection();

    $stmt = $db->query("SELECT * FROM `admin`;");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    Flight::json($users);
});


Flight::route('GET /login', function() {
    $db = Flight::db()->getConnection();

    Flight::render('login', []);
});


Flight::route('POST /login', function() {
    $db = Flight::db()->getConnection();

    $username = $_POST["username"];
    $password = md5($_POST["password"]);

    $sql = "SELECT * FROM `admin` WHERE `username` = ? AND `password` = ?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetchAll(PDO::FETCH_ASSOC);


    Flight::render('login', []);
});


Flight::route('POST /add-user', function() {
    $db = Flight::db()->getConnection();

    $name = "John Doe";
    $email = "john@example.com";

    $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");

    $stmt->execute(['name' => $name, 'email' => $email]);

    Flight::json(["message" => "User added successfully!"]);
});

Flight::route('GET /health', function() {
    Flight::json(["status" => "Service is running fine!"]);
});

Flight::route('/logout', function() {
    session_destroy();
    Flight::redirect('/');
});

Flight::start();
