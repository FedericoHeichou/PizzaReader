<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\View;

class CronCleanViews extends Command {

    protected $signature = 'cron:clean-views';
    protected $description = 'Delete IPs older than 1 week in views table';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        View::where('created_at', '<', now()->sub(1, 'week'))->delete();
    }
}
