<?php

require 'bootstrap.php';

use Src\ResponseCascadeUpdater;

$responseCascadeUpdater = new ResponseCascadeUpdater();
$responseCascadeUpdater->run();

echo "File responses updated\n";
