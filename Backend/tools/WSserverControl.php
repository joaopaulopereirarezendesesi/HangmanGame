<?php

require_once __DIR__ . '/helpers.php';

$serverScript = __DIR__ . '/../WSserver.php';

startWebSocketServer($serverScript);

function startWebSocketServer($serverScript) {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = "start cmd /k php {$serverScript}";
        pclose(popen($command, "r"));
    } else {
        $command = "gnome-terminal -- bash -c 'php {$serverScript}; exec bash'";
        exec($command);
    }
}
