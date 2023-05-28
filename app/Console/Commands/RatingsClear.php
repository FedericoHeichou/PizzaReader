<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rating;

class RatingsClear extends Command {

    protected $signature = 'ratings:clear {--days=30}';
    protected $description = 'Delete IPs older than N days [default 30] in ratings table';

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
        Rating::where('updated_at', '<', now()->sub($days, 'days'))->delete();
    }
}
