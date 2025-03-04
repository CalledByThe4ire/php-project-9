[![PHP-CI](https://github.com/CalledByThe4ire/page-analyzer/actions/workflows/php-ci.yml/badge.svg)](https://github.com/CalledByThe4ire/page-analyzer/actions/workflows/php-ci.yml)

# Page Analyzer

## About the project

This is a site that analyzes the specified pages for SEO suitability, similar to [PageSpeed Insights](https://pagespeed.web.dev/).  
The page Analyzer is a full-fledged application based on the Slim framework. Here, the basic principles of building modern websites on the MVC architecture are worked out: working with routing, query handlers and a template engine, interacting with a database.

## System requirements

- Composer 2.6.6
- PHP 8.3
- Slim 4.14
- PostgreSQL 12.20

## Installation instructions

Perform the following steps in sequence:

1. Clone the repository:

    ```bash
    git@github.com:CalledByThe4ire/page-analyzer.git
    ```

2. Go to the project directory:

    ```bash
    cd page-analyzer
    ```

3. Installing dependencies:

    ```bash
    make install
    ```

4. Export the environment variable with your data:

    ```bash
    export DATABASE_URL="postgresql://name:password@localhost:5432/database"
    ```
   or change file name .env.example to .env and set the values ​​of the DATABASE_URL variable

5. Execute all instructions from the file:

    ```bash
    psql -a -d $DATABASE_URL -f database.sql
    ```

6. Start a project:

    ```bash
    make start
    ```

7. Open in browser:

    ```bash
    http://localhost:8000
    ```

## Information about routes and methods

| Method  | Route              | Info                                       |
|---------|--------------------|--------------------------------------------|
| GET     | /                  | main page                                  |
| GET     | /urls              | getting a list of all verified sites       |
| GET     | /urls/{id}         | viewing information about the site         |
| POST    | /urls              | create a verification of the entered site  |
| POST    | /urls/{id}/checks  | run a site check                           |

### Demo:

Project [Page Analyzer](https://page-analyzer-s23b.onrender.com/) is deployed on the Render website.