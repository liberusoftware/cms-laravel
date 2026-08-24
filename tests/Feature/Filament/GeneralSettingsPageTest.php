<?php

declare(strict_types=1);

use App\Filament\Pages\ManageGeneralSettings;
use Filament\Schemas\Schema;

it('builds the general settings form with its three sections', function (): void {
    $schema = (new ManageGeneralSettings)->form(Schema::make());

    expect($schema->getComponents())->toHaveCount(3)
        ->and($schema->getComponents()[0]->getHeading())->toBe('Site Information')
        ->and($schema->getComponents()[1]->getHeading())->toBe('Social Media Links')
        ->and($schema->getComponents()[2]->getHeading())->toBe('Footer');
});
