<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminLoggerHandler extends AbstractProcessingHandler {

    public function __construct($level = Logger::DEBUG, $bubble = true, $client = null) {
        parent::__construct($level, $bubble);
    }

    public function write(array $record): void {
        $datetime = Carbon::instance($record['datetime']);
        $dir_path = "logs/admin";

        if (!Storage::exists($dir_path)) {
            Storage:: makeDirectory($dir_path);
        }

        $file_name = $dir_path . "/" . $datetime->format("Y-m-d") . ".log";
        $context = collect($record['context'])->toJson();

        $log = "[" . $datetime->format("H:i:s") . "] " . strtolower($record['level_name']) . ": " .
            $record['message'] . " - " . $context;

        Storage:: append($file_name, $log);

    }
}
