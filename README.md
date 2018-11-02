setup
=========

install php vendor packages: composer install

input configuration parametrs when prompted

if you need to edit configuration file it is located in /app/config/parameters.yml

create database: php bin/console doctrine:database:create

create tables: php bin/console doctrine:schema:update --force

start local server: php bin/console server:run

open page http://127.0.0.1:8000/ in your browser

go to teams click generate teams

go to tournaments click new tournament click add Teams

select at least 8 teams and click save

click start tournament

unimplemented

