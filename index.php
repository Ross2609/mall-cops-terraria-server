<?php

require_once __DIR__ . '/vendor/autoload.php';

use Spatie\Ssh\Ssh;
use Aws\Ec2\Ec2Client;

function sendDiscordMessage($msg, $webhook)
{
    if (isset($webhook)) {
        $curl = curl_init($webhook);
        $msg = "payload_json=" . urlencode(json_encode($msg)) . "";

        if (isset($curl)) {
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
    putenv("DISCORD_WEBHOOK_URL={$_ENV['DISCORD_WEBHOOK_URL']}");
} catch (Exception $e) {
    echo $e->getMessage();
}

// Define variables
$gotIp = false;
$publicIp = null;
$discordWebhook = getenv('DISCORD_WEBHOOK_URL');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === getenv('PASSWORD')) {
        // Get AWS instance by ID
        $ec2Client = new Ec2Client([
            'region' => 'eu-west-2',
            'version' => '2016-11-15',
            'credentials' => [
                'key' => getenv('AWS_KEY'),
                'secret'  => getenv('AWS_SECRET'),
            ]
        ]);

        $instanceIds = [getenv('INSTANCE_ID')];

        if (array_key_exists('start', $_POST)) {
            $result = $ec2Client->startInstances([
                'InstanceIds' => $instanceIds,
            ]);

            // Start Instance and get IP
            while (!$gotIp) {
                $instanceDesc = $ec2Client->describeInstances(['InstanceIds' => $instanceIds]);
                $instance = $instanceDesc['Reservations'][0]['Instances'][0];

                if (array_key_exists('PublicIpAddress', $instance)) {
                    $gotIp = true;
                    $publicIp = $instance['PublicIpAddress'];
                    var_dump($publicIp);
                }

                sleep(5);
            }

            sleep(5);

            $process = Ssh::create('ubuntu', $publicIp)
                ->disableStrictHostKeyChecking()
                ->usePrivateKey(__DIR__ . '/mall-cops-terraria-key.pem')
                ->execute(
                    [        
                        'export SCREENDIR=~/mall-cops-terraria/screens',
                        'cd mall-cops-terraria/TShock/',
                        'screen -dmS terraria bash -c \'sh startServer.sh\'',
                    ]);

            var_dump("Process success: {$process->isSuccessful()}");

            $json = '{ "username":"TerrariaBot", "content":"Server Started! IP Address: ' . $publicIp . '"}';
            $discMessage = json_decode($json, true);

            // $result = sendDiscordMessage($discMessage, $discordWebhook);
        } else {
            $result = $ec2Client->stopInstances([
                'InstanceIds' => $instanceIds,
            ]);
        }
    } else {
        echo "Password incorrect... :(";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link href="resources/styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    <title>MC Terraria Server</title>
</head>

<body>
    <div class="container">
        <h1>Mall Cops Terraria Server</h1>
        <?php if ($gotIp) : ?>
            <h2 id="server--status">Server Status: Online</h2>
            <h3 id="server--ip">IP Address: <?= $publicIp ?></h3>
        <?php else : ?>
            <h2 id="server--status">Server Status: Offline</h2>
        <?php endif; ?>
        <form method="POST">
            <div id="server--controls">
                <input class="form-control" type="password" name="password" id="password" placeholder="Password">
                <input type="submit" value="Start" name="start">
                <input type="submit" value="Stop" name="stop">
            </div>
        </form>
    </div>
</body>

</html>