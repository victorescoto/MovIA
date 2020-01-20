# MovIA

Never again be in doubt about which movie to watch next, just ask MovIA and get excellent recommendations. :)

## What's used here

- Symfony 5
- Docker
- RabbitMQ

## How to run the project

Prepare the environment by running
```bash
$ docker-compose up -d --build
```

Install the dependencies
```bash
$ docker-compose run --rm app composer install
```

Provide those configuration info in `.env`:
```
# To access the OMDB API (http://www.omdbapi.com/) and search/import movies
IMDB_API_KEY=

# Credentials info to connect on CloudAMPQ (https://www.cloudamqp.com)
CLOUD_AMPQ_HOST=
CLOUD_AMPQ_PORT=
CLOUD_AMPQ_USERNAME=
CLOUD_AMPQ_PASSWORD=
CLOUD_AMPQ_VHOST=
```
## Database configuration

For a quick example, you can use SQLite:
```
$ touch var/data.db
$ sudo chmod 777 var/data.db
$ docker-compose run --rm app php bin/console doctrine:migrations:migrate
$ docker-compose run --rm app php bin/console doctrine:fixtures:load
```

**At this point the application is ready for use \o/**

App Route list


| Name                         | Method   | Path                     |
|------------------------------|----------|--------------------------|
| app_movie_list               | GET      | /api/movies              |
| app_movie_create             | POST     | /api/movies              |
| app_movie_show               | GET      | /api/movies/{id}         |
| app_movie_update             | PUT      | /api/movies/{id}         |
| app_movie_delete             | DELETE   | /api/movies/{id}         |
| app_movieimport_import       | POST     | /api/movies/import       |
| app_rating_list              | GET      | /api/ratings             |
| app_rating_create            | POST     | /api/ratings             |
| app_rating_show              | GET      | /api/ratings/{id}        |
| app_rating_update            | PUT      | /api/ratings/{id}        |
| app_rating_delete            | DELETE   | /api/ratings/{id}        |
| app_rating_listbymovie       | GET      | /api/movies/{id}/ratings |
| app_rating_listbyuser        | GET      | /api/users/{id}/ratings  |
| app_rating_randomratings     | POST     | /api/ratings/random      |
| app_toprating_settoprating   | POST     | /api/ratings/top         |
| app_toprating_gettoprating   | GET      | /api/ratings/top         |
| app_user_list                | GET      | /api/users               |
| app_user_create              | POST     | /api/users               |
| app_user_show                | GET      | /api/users/{id}          |
| app_user_update              | PUT      | /api/users/{id}          |
| app_user_delete              | DELETE   | /api/users/{id}          |

If you prefer, you can use [Insomnia](https://insomnia.rest/) and import the file `./movia-insomnia.json`


## Last but not least

To run the *queue consumer*, please use the following command:
```
$ docker-compose run --rm app php bin/console app:start-queue-consumer
```
