<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__, 2).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->overload(
        dirname(__DIR__, 2).'/.env',
        dirname(__DIR__, 2).'/.env.test',
        dirname(__DIR__, 2).'/.env.test.functional',
    );
}

passthru(sprintf(
    'APP_ENV=%s php "%s/../bin/console" cache:clear --no-warmup',
    $_ENV['APP_ENV'],
    __DIR__
));
