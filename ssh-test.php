<?php

use Aws\Ec2\Ec2Client;
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
    putenv("DISCORD_WEBHOOK_URL={$_ENV['DISCORD_WEBHOOK_URL']}");
} catch (Exception $e) {
    echo $e->getMessage();
}

// Define variables
$gotIp = false;
$publicIp = null;
$worldName = getenv('WORLD_NAME');
$startServer = "screen -dmS terraria bash -c \"sh startserver.sh {$worldName}\"";

$process = Ssh::create('ubuntu', '35.176.227.51')
    ->usePrivateKey(__DIR__ . '/mall-cops-terraria.pem')
    ->disableStrictHostKeyChecking()
    ->execute([
        'cd mcterraria/TShock',
        $startServer,
    ]);

if ($process->isSuccessful()) {
    echo "Success:";
    print_r($process->getOutput());
} else {
    echo "Error:";
    print_r($process);
}