<?php

use Spatie\Ssh\Ssh;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

try {
    putenv("PASSWORD={$_ENV['PASSWORD']}");
    putenv("INSTANCE_ID={$_ENV['INSTANCE_ID']}");
    putenv("AWS_KEY={$_ENV['AWS_KEY']}");
    putenv("AWS_SECRET={$_ENV['AWS_SECRET']}");
    putenv("WORLD_NAME={$_ENV['WORLD_NAME']}");
} catch (Exception $e) {
    echo $e->getMessage();
}

$worldname = getenv('WORLD_NAME');
$start_server = "screen -dmS terraria bash -c \"sh startserver.sh {$worldname}\"";

$process = Ssh::create('ubuntu', '3.10.180.212')
    ->usePrivateKey(__DIR__ . '/mall-cops-terraria.pem')
    ->disableStrictHostKeyChecking()
    ->execute([
            'cd mcterraria/TShock',
            $start_server,
        ]
    );

var_dump($process);

if ($process->isSuccessful()) {
    echo "Success:";
    print_r($process->getOutput());
} else {
    echo "Error:";
    print_r($process);
}