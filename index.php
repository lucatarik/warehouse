<?php
require_once 'vendor/autoload.php';
use SimpleCrud\SimpleCrud;

$pdo = new PDO("sqlite:warehouse.db");

$db = new SimpleCrud($pdo);


//To get any table, use magic properties, they will be instantiated on demand:
$posts = $db->item->select()->run();

foreach ($posts as $post) {
    echo $post->title;
}