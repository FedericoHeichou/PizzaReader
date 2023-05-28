<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\View;

class ViewsClear extends Command {

    protected $signature = 'views:clear {--days=30}';
    protected $description = 'Delete IPs older than N days [default 30] in views table';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        $days = $this->option('days');
        $validator = \Validator::make(['days' => $days], ['days' => 'required|integer|min:1']);
        if ($validator->fails()) {
            $this->error('Invalid days value');
            return;
        }
        View::where('created_at', '<', now()->sub($days, 'days'))->delete();
    }
}
