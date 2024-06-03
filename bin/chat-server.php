<?php 
use Ratchet\Server\IoServer;
use App\WebSocket\Chat;

require __DIR__ . '/../vendor/autoload.php';

$server = IoServer::factory(
    new Chat(),
    8000
);

$server->run();