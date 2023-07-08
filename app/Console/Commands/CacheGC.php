<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class CacheGC extends Command {

    protected $signature = 'cache:gc {--progress}?';
    protected $description = 'Clear all dangling cache files';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        $progress = $this->option('progress');

        $expired_file_count = 0;
        $empty_folders_count = 0;
        $now = Carbon::now()->timestamp;

        $cache_dir = config('cache.stores.file.path');
        if (!is_dir($cache_dir)) {
            echo "Cache directory $cache_dir does not exist.\n";
            return;
        }

        if (is_link($cache_dir)) {
            $cache_dir = realpath($cache_dir);
        }

        $storage_path = realpath(Storage::path('')) . '/';
        if (strpos($storage_path, $cache_dir) === 0) {
            echo "Cache directory $cache_dir is a parent of storage directory $storage_path.\n";
            return;
        }


        $it = new \RecursiveDirectoryIterator($cache_dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        $folder_is_empty = true;
        foreach($files as $file) {
            if ($file->getFileName() == '.gitignore') {
                $folder_is_empty = false;
                // echo "Skipped .gitignore file.\n";
                continue;
            }

            $path = $file->getPathName();
            if ($file->isLink()) {
                // echo "Deleted symlink $path.\n";
                unlink($path);
            } else if ($file->isFile()) {
                try {
                    $expire = file_get_contents($path, false, null, 0, 10);
                    if (is_numeric($expire) && $expire == intval($expire) && $now >= $expire) {
                        // echo "Deleted expired cache file $path.\n";
                        unlink($path);
                        $expired_file_count++;
                    } else {
                        $folder_is_empty = false;
                    }
                } catch (Exception $e) {
                    $folder_is_empty = false;
                }
            } else if ($file->isDir()){
                if ($folder_is_empty) {
                    try {
                        // echo "Deleted empty cache folder $path.\n";
                        rmdir($path);
                        $empty_folders_count++;
                    } catch (Exception $e) {
                        // ignore, it is probably a root folder
                    }
                }
                $folder_is_empty = true;
            }

            if ($progress && $expired_file_count % 10 === 0) {
                echo "\rProgress: deleted $expired_file_count expired files, $empty_folders_count empty folders";
            }
        }
        if ($progress) {
            echo "\n";
        }

        echo "Deleted $expired_file_count expired cache files.\n";
        echo "Deleted $empty_folders_count empty cache folders.\n";
    }
}
