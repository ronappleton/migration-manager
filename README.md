# Migration Manager

This is the Migration Manager app base.

This API stack includes:

 - PHP 8.1
 - Mysql 8.0
 - Redis
 - Laravel Passport
 - Laravel Horizon
 - Laravel Telescope
 - Laravel Sail
 - Mail Hog
 - Code Coverage

# Installation

Do usual, run composer, cp .env.example and add

`alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'`

to your terminal profile zshrc, bash_rc or whatever.

# Sail

To use sail:
 - composer sail-up (runs as daemon)
 - composer sail-down

If you have issues with connections after changing ports:
 - sail down -v

Run artisan commands via sail:
 - sail artisan migrate

etc, etc.
