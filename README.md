# PizzaReader
A Manga and Comics reader written in Laravel

# Important
If you get forbidden 403 with images perform manually `cd public; ln -s ../storage/app/public storage`

# Installation
```bash
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
