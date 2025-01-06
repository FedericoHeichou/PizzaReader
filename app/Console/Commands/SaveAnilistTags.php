<?php

namespace App\Console\Commands;

use App\Anilist\Api;
use App\Models\Anilist\Genre;
use App\Models\Anilist\Tag;
use Illuminate\Console\Command;

class SaveAnilistTags extends Command
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
    protected $signature = 'app:save-anilist-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tags = $this->api->getTags();
        foreach ($tags as &$tag) {
            $tag['anilistId'] = $tag['id'];
            unset($tag['id']);
        }
        Tag::upsert($tags, ['name', 'anilistId'], ['description']);
    }
}
