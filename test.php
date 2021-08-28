<?php

use Spatie\Ssh\Ssh;

require_once __DIR__ . '/vendor/autoload.php';

$process = Ssh::create('ubuntu', '18.170.220.42')
    ->usePrivateKey(__DIR__ . '/mall-cops-terraria.pem')
    ->disableStrictHostKeyChecking()
    ->execute([
            'cd mcterraria/TShock',
            'screen -dmS terraria bash -c "sudo mono TerrariaServer.exe"',
        ]
    );


if ($process->isSuccessful()) {
    echo "Success:";
    print_r($process->getOutput());
} else {
    echo "Error:";
    print_r($process);
}