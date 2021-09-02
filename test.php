<?php

use Spatie\Ssh\Ssh;

require_once __DIR__ . '/vendor/autoload.php';

function sendDiscordMessage($msg, $webhook) {
    if(isset($webhook)) {
        $curl = curl_init($webhook);
        $msg = "payload_json=" . urlencode(json_encode($msg))."";
        
        if(isset($curl)) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $msg);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl);
            curl_close($curl);

            return $response;
        }
    }
}

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

$discordWebhook = getenv('DISCORD_WEBHOOK_URL');

$json = '{ "username":"TerrariaBot", "content":"Server Started! IP Address:"}';
$discMessage = json_decode($json);

$result = sendDiscordMessage($discMessage, $discordWebhook);
var_dump($result);