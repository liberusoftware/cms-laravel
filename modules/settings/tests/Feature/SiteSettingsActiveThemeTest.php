<?php

use Liberu\Foundation\Settings\Settings\SiteSettings;

it('exposes active_theme defaulting to the default theme', function (): void {
    expect(app(SiteSettings::class)->active_theme)->toBe('default');
});
