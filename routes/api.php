<?php

foreach (glob(app_path('Modules/*/routes.php')) as $routeFile) {
    require $routeFile;
}
