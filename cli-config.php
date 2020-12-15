<?php
$app = require __DIR__.'/src/bootstrap/app.php';
$app->boot();

$entityManager = $app->getEntityManager();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);

