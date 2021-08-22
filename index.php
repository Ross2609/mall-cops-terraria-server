<?php

if (file_exists(__DIR__ . '/.env')) {
    // This is for locally running the project only, none of this is needed for production
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    try {
        $dotenv->required(['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'PASSWORD', 'INSTANCE_ID', 'RAM_IN_GB'])->notEmpty();
        putenv("AWS_ACCESS_KEY_ID={$_ENV['AWS_ACCESS_KEY_ID']}");
        putenv("AWS_SECRET_ACCESS_KEY={$_ENV['AWS_SECRET_ACCESS_KEY']}");
        putenv("PASSWORD={$_ENV['PASSWORD']}");
        putenv("INSTANCE_ID={$_ENV['INSTANCE_ID']}");
        putenv("RAM_IN_GB={$_ENV['RAM_IN_GB']}");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    die(getenv('PASSWORD'));
    if ($_POST['password'] === 'CROC') {
        echo "Password correct! :)";
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
    <link href="../resources/styles.css" rel="stylesheet" >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    <title>Terraria Server</title>
</head>
<body>
    <h2 id="server--status">Server Status: Offline</h2>
    <form method="POST">
        <div id="server--controls">
            <input class="form-control" type="password" name="password" id="password" placeholder="Password">
            <input type="submit" value="Start">
        </div>
    </form>
</body>
</html>