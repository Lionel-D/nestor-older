<?php

$commands = [
    'php "%s/../bin/console" cache:clear --no-warmup',
//    'php "%s/../bin/console" doctrine:database:drop --force',
//    'php "%s/../bin/console" doctrine:database:create',
//    'php "%s/../bin/console" doctrine:migrations:migrate --no-interaction',
//    'php "%s/../bin/console" doctrine:fixtures:load --no-interaction',
];

$prefix = '';

if (isset($_ENV['BOOTSTRAP_LOCAL_TEST_ENV'])) {
    $localTestEnv = $_ENV['BOOTSTRAP_LOCAL_TEST_ENV'];
    $prefix       = 'APP_ENV=%s ';
}

foreach ($commands as $command) {
    if ($prefix !== '') {
        passthru(sprintf(
            $prefix.$command,
            $localTestEnv,
            __DIR__
        ));
    } else {
        passthru(sprintf(
            $command,
            __DIR__
        ));
    }
}

require __DIR__.'/../config/bootstrap.php';
