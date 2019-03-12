<?php
require_once "./vendor/autoload.php";

use App\Kernel;

$kernel = new Kernel();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($kernel->container->get('entity_manager'));



//to recreate database as you modify entities

// vendor/bin/doctrine orm:schema-tool:drop --force
// vendor/bin/doctrine orm:schema-tool:create

// OR

// vendor/bin/doctrine orm:schema-tool:update --force

//vendor/bin/doctrine orm:schema-tool:update --force --dump-sql
//Specifying both flags --force and --dump-sql will cause the DDL statements to be executed and then printed to the screen.
