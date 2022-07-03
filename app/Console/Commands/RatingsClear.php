<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rating;

class RatingsClear extends Command {

    protected $signature = 'ratings:clear';
    protected $description = 'Delete IPs older than 1 week in ratings table';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        Rating::where('updated_at', '<', now()->sub(1, 'week'))->delete();
    }
}
