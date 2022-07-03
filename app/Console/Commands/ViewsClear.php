<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\View;

class ViewsClear extends Command {

    protected $signature = 'views:clear';
    protected $description = 'Delete IPs older than 1 week in views table';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        View::where('created_at', '<', now()->sub(1, 'week'))->delete();
    }
}
