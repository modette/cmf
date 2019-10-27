<?php

use Tests\Modette\Http\HttpTestsHelper;
use Tests\Modette\Monorepo\MonorepoTestsHelper;

require_once __DIR__ . '/../vendor/autoload.php';

MonorepoTestsHelper::generateLoader();
HttpTestsHelper::generateLoader();
