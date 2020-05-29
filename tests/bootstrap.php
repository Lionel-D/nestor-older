<?php

$commands = ['php "%s/../bin/console" cache:clear --no-warmup'];

if (isset($_ENV['BOOTSTRAP_LOCAL_TEST_ENV'])) {
    $localTestEnv = $_ENV['BOOTSTRAP_LOCAL_TEST_ENV'];
    $prefix = 'APP_ENV=%s ';
}

foreach ($commands as $command) {
    if (isset($prefix) && isset($localTestEnv)) {
        passthru(sprintf($prefix.$command, $localTestEnv, __DIR__));
    } else {
        passthru(sprintf($command, __DIR__));
    }
}

require __DIR__.'/../config/bootstrap.php';
