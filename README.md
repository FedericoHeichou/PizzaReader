# <p align="center">![PizzaReader Logo](storage/app/public/img/logo/PizzaReader-128.png)<br />PizzaReader</p>
<p align="center">
    <img alt="Latest version" src="https://img.shields.io/badge/stable-v1.2.0-blue">
    <img alt="PHP Version Support" src="https://img.shields.io/badge/php-%3E%3D8.0-blue">
    <img alt="Laravel version" src="https://img.shields.io/badge/laravel-%5E9.19-lime">
    <img alt="License" src="https://img.shields.io/badge/license-GPL-3.0-green"></p>

# About PizzaReader
A manga and comic reader written in Laravel and Vue.  
It is used from 2020 by many italian scanlation teams, like [lupiteam.net](https://lupiteam.net), [phoenixscans.com](https://www.phoenixscans.com) and [gtothegreatsite.net](https://reader.gtothegreatsite.net), that is the reason why it is called PizzaReader.  
It supports [Tachiyomi](https://tachiyomi.org/), a manga reader Android application and I created there a [multisrc class](https://github.com/tachiyomiorg/tachiyomi-extensions/tree/master/multisrc/overrides/pizzareader) to add your reader there too.  
The admin panel is inspired by the old famous [FoOlSlide2](https://github.com/chocolatkey/FoOlSlide2) because I started to develop this reader for personal use, but other italian teams contacted me and asked to install it on their websites.  
I made this reader publicly available in May 2023.

Current available languages:
- english
- italian

To add other languages feel free to submit pull requests copying from [it.json](lang/it.json) and [it.js](resources/js/lang/it.js)


## Secondary features
You can add custom HTML (which includes js and css code) in many pages enabling the "EDIT_CUSTOM_HTML" feature in your `.env` file.  
When you finished to edit the HTML in the settings page you should disable this feature back.  
It supports (via settings) the PDF and ZIP downloads of chapters/volumes.  

## Screenshots
Click the images to open it.

<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/9b0352d7-f9c3-46a3-b5e4-6202320ab426" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/9568bd7c-0e49-47e8-a767-1e6fc5deed6a" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/b7e1174f-0110-4725-8929-1fdca2fca7cc" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/015a924f-f146-4bb4-af50-7bcfe1abebcd" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/a06ffe54-2f5a-4ffd-8645-161b07408622" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/74960500-6f0d-44bd-922a-4435fd48f9aa" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/a7b6de1d-9350-471a-8b1a-77410c3e1f01" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/7c83b110-1f10-4501-9134-22479c577688" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/d67a6132-e7a9-4954-9d48-d9b1306246f7" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/59abed8a-b809-4a74-b029-1b1c0e453e37" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/0f6959d9-1699-4ad7-a951-fd83b2b23745" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/d884d670-4d17-4209-be33-93b06eb6ecf1" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/5c6dc306-4059-4c0c-9cf0-7ac579ed133d" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/f42b3da4-9832-4a43-abf9-223d020270ee" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/783fbfae-77c8-4cae-a43e-74c2e9cc1108" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/4940c037-0f8f-4a5f-9de0-f09b0c9689d4" width="300" />

# Current version
The `master` version usually is stable because branches are merged after being tested in production on differents websites.  
If you see my last commit is old, you can consider the `master` branch stable, I just forgot to tag.  
Sometime I merge dependabot's pull requests on `master` without rebuilding the application because most of them are only for development.

The current stable version is based on Laravel 9 and requires PHP >=8.0.  
Older versions are not mainted at all.

# Installation
## Important
If you get forbidden 403 with images perform manually `cd public; ln -s ../storage/app/public storage`

## How to
```bash
# If you want you can specify a tag
git clone https://github.com/FedericoHeichou/PizzaReader.git pizzareader
cd pizzareader

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

cd public; ln -s ../storage/app/public storage
sed -i 's/RewriteEngine On/RewriteEngine On\n    RewriteCond %{HTTP_HOST} ^\(.+\)\\.reader\\.pizzareader\\.local$   [NC]\n    RewriteRule ^\(.*\)$ https:\/\/reader\\.pizzareader\\.local%{REQUEST_URI} [R=301,QSA,NC,L]/g' .htaccess
cd ..

php composer.phar install --no-dev
cp .env.example .env
sed -i 's/^\(APP_NAME=\).*$/\1PizzaReader/' .env
sed -i "s,^\(APP_URL=\).*$,\1https:\/\/reader.pizzareader.local," .env
sed -i "s/^\(DB_HOST=\).*$/\1pizzareader.mysql.db/" .env
sed -i "s/^\(DB_PORT=\).*$/\13306/" .env
sed -i "s/^\(DB_DATABASE=\).*$/\1pizzareader/" .env
sed -i "s/^\(DB_USERNAME=\).*$/\1pizzareader/" .env
sed -i "s/^\(DB_PASSWORD=\).*$/\1password/" .env

sed -i "s/APP_ENV=production/APP_ENV=local/" .env
# sed -i "s/APP_DEBUG=false/APP_DEBUG=true/" .env
php composer.phar dump-autoload
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan config:cache
php composer.phar dump-autoload
sed -i "s/APP_ENV=local/APP_ENV=production/" .env
# sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env

find . -type d -exec chmod 0755 {} \;
find . -type f -exec chmod 0644 {} \;
# Maybe you need to chmod/chown the upload's directory
```

# Update
How to update your reader:
```bash
php artisan down --render='maintenance' --secret='YourCustomSecret' || php artisan down
# You can bypass maintenance mode by visiting your site with the secret URI you choose, for example https://pizzareader.local/YourCustomSecret
git pull origin master
php composer.phar install --no-dev
php artisan cache:clear
php artisan config:cache
php artisan migrate
php artisan up
```
# Cron
To run crons add to the local crontab
```bash
* * * * * cd /path-to-your-reader && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

If you have not a local crontab run in a remote one
```bash
* * * * * /usr/bin/curl "https://your-reader.local/cron.php?t=$(date +%s)" 2>&1
```

# FAQ
Sometime I add features or bugfix who requires you to run manually certain commands for readers updated from old versions.  
You can read these FAQs to findout if you have some of problems listed here and how to solve them.

## There are orphaned ZIPs
First execute the command with `--dry-run` to emulate the clear:
```bash
php artisan downloads:clear --dry-run
```
Then if you like the output run:
```bash
php artisan downloads:clear
```

## Some comics covers are not resized
If your homepage is very heavy because your tumbnails are too big, run:
```
php artisan thumbnail:resize
```
Thumbnail with a small size will be regenerated.  
Usually there should be already resized thumbnail, but comics created in a very old version of the reader could not have it.

## Manually clear views table
If your views table is very big, it means you are not running a [cron](#cron).  
Anyway you can manually clear views table:
```bash
php artisan views:clear
```
It will remove all views older than 1 week.  
**Note**: it will not decrease the views counter, it only remove the combinations of `(ip, chapter)`, which means a user who read a chapter one week ago will increase the counter again in the next visit.

## Manually clear rating table
I don't suggest to perform this action if your table is not so big, but `sums` and `counts` used to calculate the average ratings are stored with the chapters too.  
The rating table has three purposes:
- preventing an IP address to vote the same chapter multiple times
- as a fallback for chapters who are voted last time in a old version of the reader (<=v1.0.1)
- as a fallback if a row of the `chapters` table is broken (for example if someone manually set `rating_sum` to 0 and users lower their rating it prevents a negative `rating_sum` and recalculate them from the `ratings` table).

If you don't care and still need to clear the table I suggest to perform a backup of `chapters` and `ratings` tables then run:
```bash
php artisan ratings:clear
```
If you need to recover a deletion, reimport the `ratings` table then set to 0 all `rating_count` of chapters you need to recalculate; the next vote will force a recalculation of sums, counts and averages of voted chapter.

## Some pages are not in the filesystem
Sometime for reasons some pages are missing in the filesystem (for example you uploaded some chapters with unstable connection without checking the result).  
This command will get all pages in the database and checks if they exists in the filesystem.  
The missing pages will be printed.  
Adding `--csv` will show the comics, chapter and page in a human readable mode with a link to the missing page instead of the only file's absolute path (you can normally open it with Excel too).
```bash
php artisan pages:check
php artisan pages:check --csv
```

# Donations
Donations are appreciated, feel free to contact me at the email listed in my profile: [FedericoHeichou](https://github.com/FedericoHeichou).
