<?php

namespace App\Logging;

use Monolog\Logger;

class AdminLogger {
    /**
     * Create a custom Monolog instance.
     *
     * @param array $config
     * @return Logger
     */
    public function __invoke(array $config): Logger {
        return new Logger("admin", [new AdminLoggerHandler(),]);
    }
}
