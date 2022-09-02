# Backend Blog

## Tech Stacks

-   🛠 [Laravel 8 with a minimum of PHP 7](http://https://laravel.com/docs/8.x/ "Laravel with a minimum of PHP 7")
-   ⚡️ [Laravel Fortify with Sanctum ](https://laravel.com/docs/8.x/fortify "Laravel Fortify ")
-   🗂 [guzzle http Client](https://docs.guzzlephp.org/en/stable/ "guzzle http Client")
-   🛣 [Bugsnag for error Reporting](https://www.bugsnag.com/ "Bugsnag for error Reporting")
-   🎨[Pest php for Testing with PHP unit](https://pestphp.com/ "Pest php for Testing with PHP unit")

## Getting Started

### Prerequisites

-   You should at least have php 7 in your Machine
-   Database used is MySql
-   Cache Driver is Local that should be fine for testing
-   This repo provides a Rest API for the frontend Project .

### Steps

-   Clone the repository
-   Navigate to the project directory and run the following command to get started

```sh
composer install
```

-   Configure the database by creating a db named `blogger` in your `DBMS` in this case `MySql`
-   if the above Steps went great, then navigate to the project and run your migrations

```sh
php artisan migrate:fresh --seed
```

-   At this point you should have a tables with dummy data along with an admin user created as the default first user. Credentials for the admin can be viewed by Navigating to `database/seeders/DatabaseSeeder.php` from the project root.

### Integration with frontend

-   At this point you can configure the frontend to be within the same domain as your backend service.
    so assuming the backend runs on `http://localhost` and frontend running at `http://localhost:3000` then you neednt have to touch your `.env ` file . Otherwise you would need to configure the env variables accordingly so that they can match. Failure to do this all efforts to communicate with the api from the fronted point will fail.

### Features

---

> > ##### View Blog Posts / Post
> >
> > > ##### Registration of new Users
> > >
> > > > ##### Login with fro registered Users
> > > >
> > > > > ##### Get all Posts() & Single Post
> > > > >
> > > > > > ##### Get all system authors(`api/authors`) / user(`api/authors/1`)
> > > > >
> > > > > ##### View Posts by Author
> > > >
> > > > ##### Search Articles
> > >
> > > ##### Sort articles by Publication Date
> >
> > ##### Query Posts from third party ( must be logged on as admin )

---

### **Custom Commands**

-   Run the following command to flush out the active sessions in the system (while testing)`php artisan squared:clear-session`

-   Run the following command to query posts from third party `php artisan squared:fetch-posts `
    Additionally there is a scheduler that is configured to run every thirty minutes, you can test the functionality by reducing frequency to say `everyminute()` and see it action.

---

### Tests

Tests can be found in the tests directory whn you are in the root project. Tests Some tests are written with PHP unit and and others are written in [PEST PHP](https://pestphp.com/ "PEST PHP")
to get configure `pest` please follow the instructions from their official documentation..

-   When you are done with the setup you can run`php artisan test ` to see the tests that are available

#### For any queries, Please reach out.
