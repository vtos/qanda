<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Q&A app made with Laravel + Artisan

Welcome to Q&A app made with Laravel! This guide contains instruction to run the app using Docker and also some reasoning
on approaches which were used to implement it.

## Running with Docker

1. Run ```git clone https://github.com/vtos/laravel-qanda.git qanda``` to clone the repository.
2. ```cd``` into the created ```qanda``` dir. Create ```.env``` and ```.env.testing``` files in the project root. Make use of ```.env.example```
and ```.env.testing.example``` to create those files. Make sure to fill ```DB_USERNAME```, ```DB_PASSWORD```, ```TEST_DB_USERNAME```,
and ```TEST_DB_PASSWORD``` values in ```.env```, and also ```DB_USERNAME``` and ```DB_PASSWORD``` in ```.env.testing```
file. **Important!** Make sure ```DB_USERNAME``` and ```DB_PASSWORD``` in ```.env.testing``` equal the corresponding values of
```TEST_DB_USERNAME``` and ```TEST_DB_PASSWORD``` in ```.env```.
3. Run Composer's ```install``` to install the dependencies. From now on we can use Laravel Sail
   to run commands in the container.
4. Run ```./vendor/bin/sail up```.
5. Run ```./vendor/bin/sail php artisan migrate``` to install the database.
6. Run ```./vendor/bin/sail php artisan test``` to run the tests.
7. Run ```./vendor/bin/sail php artisan qanda:interactive``` to run the console app.

## Some thoughts on the project

Being a fan of Domain Driven Design from recently I tried to introduce this approach to the project. Though,
the current implementation isn't a full-featured version of DDD (the project is obviously too small for it), it contains
just several concepts from it here like introducing the domain of the application, using value objects, data transfer
objects and applying command-and-handler pattern to implement essential domain processes (like creating or answering
a question). The main purpose is to demonstrate an approach when the business logic of the application can be separated
from Laravel models, which allows for cleaner code in models, code re-use and writing unit tests which are not bound
to a certain database.

The project doesn't introduce a user model to allow for unlimited number of users to use the application. This could be
the next step in development of the app. Currently, question model and question attempt model have a 'one-to-one'
relation defined. It means that the use who creates a question can practice it himself then. It would be quite logical
to introduce roles to allow for question creation by a dedicated user (aka the admin), while other users can only
practice the questions created.

Although the current version of the app operates via CLI only, the database flow is built with an 'HTTP mindset'.
It means that it contains numerous database queries which are unnecessary when the app runs in CLI mode.
It would be wise to introduce one more data storage layer for this specific CLI case to allow for storing the data
in memory while the app runs in CLI, instead of querying the database each time we need some data. But the current
app version doesn't implement this layer.

The project is bundled with three types of tests: unit, feature and end-to-end. End-to-end contain only one test
which tests some actions performed by a user via CLI, the coverage isn't 100%.

Project files contain some inline comments and PHPDoc Blocks to clarify some decisions used, though it doesn't have
100%-coverage, so I would be happy to answer any questions on the project structure.
