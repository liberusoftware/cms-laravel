<?php

declare(strict_types=1);

return ['default_disk' => env('CMS_BACKUP_DISK', 'local'), 'default_retention_days' => 30, 'max_retention_days' => 3650];
