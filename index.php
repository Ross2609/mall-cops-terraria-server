<?php

require_once __DIR__ . '/vendor/autoload.php';

use Aws\Ec2\Ec2Client;

// This is for locally running the project only, none of this is needed for production
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

try {
    putenv("PASSWORD={$_ENV['PASSWORD']}");
    putenv("INSTANCE_ID={$_ENV['INSTANCE_ID']}");
    putenv("AWS_KEY={$_ENV['AWS_KEY']}");
    putenv("AWS_SECRET={$_ENV['AWS_SECRET']}");
} catch (Exception $e) {
    echo $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === getenv('PASSWORD')) {
        $action = 'START';

        // Get AWS instance by ID
        $ec2Client = new Aws\Ec2\Ec2Client([
            'region' => 'eu-west-2',
            'version' => '2016-11-15',
            'credentials' => [
                'key' => getenv('AWS_KEY'),
                'secret'  => getenv('AWS_SECRET'),
            ]
        ]);

        $instanceId = getenv('INSTANCE_ID');

        $instanceIds = [getenv('INSTANCE_ID')];
        
        if(array_key_exists('start', $_POST)) {
            $result = $ec2Client->startInstances([
                'InstanceIds' => $instanceIds,
            ]);
        } else {
            $result = $ec2Client->stopInstances([
                'InstanceIds' => $instanceIds,
            ]);
        }

        $monitorInstance = 'ON';

        if ($monitorInstance == 'ON') {
            $result = $ec2Client->monitorInstances(array(
                'InstanceIds' => $instanceIds
            ));
        } else {
            $result = $ec2Client->unmonitorInstances(array(
                'InstanceIds' => $instanceIds
            ));
        }

        var_dump($result);

        // Start Instance and get IP
        

        // SSH into server using ip

    }
    else {
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
    <link href="resources/styles.css" rel="stylesheet" >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    <title>MC Terraria Server</title>
</head>
<body>
    <div class="container">
        <h1>Mall Cops Terraria Server</h1>
        <h2 id="server--status">Server Status: Offline</h2>
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