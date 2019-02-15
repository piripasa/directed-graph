## PHP Assessment Graph Service API

### Framework & tools

- Lumen 5.6 (PHP framework)
- MySQL (for storage engine)
- Redis & Socket.IO (for realtime update) 
- Composer (for installing dependencies)
- npm (for installing node dependencies)

Unit test cases: /tests/Unit/


### Installation
This is a dockerized application. Do the following

Make sure: 
* `docker` & `docker-compose` installed in your PC.

To do:

- `cd graph_api/` into the project root directory.
- `docker-compose up -d --build`
- `sudo docker ps` (to see the docker containers)
- `sudo docker exec -it <php-container-id> /bin/sh`
- `cp .env.docker .env`
- `composer install`
- `chgrp -R www-data storage bootstrap/cache`
- `chmod -R ug+rwx storage bootstrap/cache`
- `php artisan migrate`
- `vendor/bin/phpunit` for PHPUnit test

Modify redis host on file socket.js line 6 to redis
 
 API base url `http://127.0.0.1:80`.


#### Without Docker
Make Sure you have installed in your PC:

- PHP >= 7.0.0
- MySQL >= 5.6
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Composer (https://getcomposer.org/)
- Redis
- npm

To do - RUN following from project terminal:

- `cd graph_api/` into the project root directory.
- `cp .env.example .env` (DB, Redis config) 
- `composer install`
- `sudo chgrp -R www-data storage bootstrap/cache`
- `sudo chmod -R ug+rwx storage bootstrap/cache`
- `php artisan migrate`
- `vendor/bin/phpunit` for PHPUnit test
- `npm install`
- `node socket.js`
- `php artisan serve` (separate terminal)

 API base url `http://127.0.0.1:8000`.
 
 
 
 ### Open base url in browser and see real time update
 
 ### CheckList
 
 - [x] CRUD REST API
 - [x] Real time update
 - [x] Get Shortest path​ ​between two nodes
 - [x] Web client
 - [x] API doc
 
 #### Postman API collection (you may need to change base url)
 https://www.getpostman.com/collections/563bc575971eb017ec99

