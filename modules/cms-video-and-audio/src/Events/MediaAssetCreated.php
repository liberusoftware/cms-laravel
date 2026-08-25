<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Events;

use Liberu\Cms\VideoAndAudio\Models\MediaAsset;

final readonly class MediaAssetCreated { public function __construct(public MediaAsset $asset) {} }
