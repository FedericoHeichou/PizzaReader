# <p align="center">![PizzaReader Logo](storage/app/public/img/logo/PizzaReader-128.png)<br />PizzaReader</p>
<p align="center">
    <img alt="Latest version" src="https://img.shields.io/badge/stable-v1.0.0-blue">
    <img alt="PHP Version Support" src="https://img.shields.io/badge/php-%3E%3D7.4-blue">
    <img alt="Laravel version" src="https://img.shields.io/badge/laravel-%5E8.0-lime">
    <img alt="License" src="https://img.shields.io/badge/license-Apache 2-green"></p>

# About PizzaReader
A Manga and Comics reader written in Laravel

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
php artisan down --render='maintenance' || php artisan down
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
If your views table is very bug, it means you are not running a [cron](#cron).  
Anyway you can manually clear views table:
```bash
/usr/bin/php artisan views:clear
```
It will remove all views older than 1 week.  
**Note**: it will not decrease the views counter, it only remove the combinations of `(ip, chapter)`, which means a user who read a chapter one week ago will increase the counter again in the next visit.

## Manually clear rating table
I don't suggest to perform this action if your table is not so big, but `sums` and `counts` used to calculate the average ratings are stored with the chapters too.  
The rating table has three purposes:
- preventing an IP address to vote the same chapter multiple times
- as a fallback for chapters who are voted last time in a old version of the reader (<=v1.0.1)
- as a fallback if a row of the `chapters` table is broken (for example if someone manually set `rating_sum` to 0 and users lower their rating it prevents a negative `rating_sum` and recalculate them from the `ratings` table)

If you don't care and still need to clear the table I suggest to perform a backup of `chapters` and `ratings` tables then run:
```bash
/usr/bin/php artisan ratings:clear
```
If you need to recover a deletion, reimport the `ratings` table then set to 0 all `rating_count` of chapters you need to recalculate; the next vote will force a recalculation of sums, counts and averages of voted chapter.
