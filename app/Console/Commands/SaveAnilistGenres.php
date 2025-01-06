<?php

namespace App\Console\Commands;

use App\Anilist\Api;
use App\Models\Anilist\Genre;
use Illuminate\Console\Command;

class SaveAnilistGenres extends Command
{
    public function __construct(private Api $api)
    {
        parent::__construct();
    }


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-anilist-genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $genres = $this->api->getGenres();
        $tmp = [];
        foreach ($genres as $genre) {
            $tmp[] = ['name' => $genre];
        }
        Genre::upsert($tmp, ['name'], ['name']);
    }
}
