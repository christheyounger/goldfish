Goldfish Project Management
===========================
_Oooh, a castle!_

Goldfish is a project manager that attempts to combine the features of several web technologies into a single package.

It isn't really inteded for use, rather as a project for me to cut my teeth on new technologies, but if you like it, great!

1) Installing
-------------

Clone the repo

    git clone https://github.com/darkbluesun/goldfish

## Create a database

Using your favourite relational database, create a database for Goldfish. You might also want to create a user with access only to this database, or not. You'll be asked for these details at the end of the next step.

### Install Dependancies

As Symfony uses [Composer][2] to manage its dependencies, the recommended way
to create a new project is to use it.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -s http://getcomposer.org/installer | php

The following command will install all the server-side dependancies.

    php composer.phar install

At the end of that process you'll be asked for a number of local settings, including the database you created and user credentials to access it.

## Building the database

Creating the (relatively few) tables that Goldfish uses is a one-step process, simply run:

    app/console doctrine:schema:update --dump-sql --force

This will use the credentials and database you specified to build the tables. No data will be created yet. The `--dump-sql` switch simply prints out what the script is doing. It's nice to see details.

2) Checking your System Configuration
-------------------------------------

Before starting coding, make sure that your local system is properly
configured for Symfony.

Execute the `check.php` script from the command line:

    php app/check.php

The script returns a status code of `0` if all mandatory requirements are met,
`1` otherwise.

Access the `config.php` script from a browser:

    http://localhost/path-to-project/web/config.php

If you get any warnings or recommendations, fix them before moving on.


3) Install client-side libraries
--------------------------------

This project uses a lot of client-side web technologies to make interactions nicer.

To install these libraries you will need to install [Node.js][3].

Once installed, run the following command to install tools:

    npm install

Once that runs successfully, you can run bower to install the libraries:

    bower install

Finally, when all that is done, run Gulp to build your assets.

    gulp

[1]:  http://symfony.com/doc/2.5/book/installation.html
[2]:  http://getcomposer.org/
[3]:  http://nodejs.org/
