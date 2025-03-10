<?php

declare(strict_types=1);

// Long-running Cylinder processes should not end up
// actually executing the SCRIPT_FILENAME.
throw new Exception('I should not actually be used');
