<?php

shell_exec("php " . __DIR__ . "/../index.php");
shell_exec("cd " . __DIR__ . "/../ && git add . && git commit -m \"" . date("Y-m-d h:i:s") . "\" && git push -f ");
