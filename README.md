<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Chibuikem's todo List

This is an advanced API based todo list built with the laravel framework. it enables users to schedule and manage 
tasks in the todo list, it utilizes laravel's route model binding for it's API routes.
 it's features includes:

- [Simple, fast routing engine based on laravel route model binding]<!-- (https://laravel.com/docs/routing). -->
- [Authentication]<!-- (https://laravel.com/docs/container). -->
- session management<!-- (https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage. -->
- database support (SQL)<!-- (https://laravel.com/docs/eloquent). -->
- API documentation<!-- (https://laravel.com/docs/migrations). -->
- Search functionality<!-- (https://laravel.com/docs/migrations). -->
- Usage statistics

it is accessible, powerful, and provides support larger scaling and implementing a more robust applications.

## Learning Laravel
The application is easy to use and implement, its routes are well documented and they include:
- **POST register**
- **POST  login**
- **POST  Get tasks**
- **POST  create-todo-list**
- **PUT  get-task**
- **PUT  update task**
- **DEL  delete task**
- **GET  start task**
- **GET  completed task**
- **POST get task by label**
- **POST get task by status**
- **GET  get user info**
- **GET  get user statistics**
- **POST search for tasks**

## how to install
**1. Clone GitHub repo for this project locally**
**2. cd into your project**
**3. Install Composer Dependencies: using the "composer install" command**
**4. Create a copy of your .env file**
**5. Generate an app encryption key: using "php artisan key:generate" command**
**6. Create an empty database for our application**
**7. In the .env file, add database information to allow Laravel to connect to the database**
**8. Migrate the database: using "php artisan migrate" command**
**9. [Optional]: Seed the database: using "php artisan db:seed" command**
