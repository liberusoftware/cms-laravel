#!/usr/bin/env bash

set -euo pipefail

echo '== Composer metadata and security =='
composer validate --no-check-publish
composer audit --locked --no-interaction

echo '== Independent module metadata and boundaries =='
for package_manifest in modules/*/composer.json; do
    package_dir="${package_manifest%/*}"

    jq -e '.name | startswith("liberusoftware/module-")' "$package_manifest" >/dev/null
    test -f "$package_dir/README.md"
    test -f "$package_dir/CHANGELOG.md"
    test -f "$package_dir/module.json"
    jq -e --arg package "$(jq -r '.name' "$package_manifest")" '.package == $package' "$package_dir/module.json" >/dev/null

    if rg -n '(^|[^A-Za-z])App\\' "$package_dir/src"; then
        echo "Application coupling found in $package_dir" >&2
        exit 1
    fi
done

echo '== PHP quality =='
vendor/bin/pint --test
vendor/bin/phpstan analyse --no-progress --memory-limit=1G
vendor/bin/rector process modules --dry-run --no-progress-bar
php -l artisan

echo '== Architecture and application tests =='
php artisan test \
    tests/Feature/Cms/ArchitectureTest.php \
    tests/Feature/Cms/RemovabilityTest.php \
    tests/Feature/Cms/EmbeddabilityTest.php

echo '== Full release-scope tests and coverage =='
XDEBUG_MODE=coverage php -d memory_limit=-1 artisan test \
    --coverage-clover=coverage.xml \
    --min=100

echo '== Frontend assets =='
npm ci --no-audit --no-fund
npm run build

echo '== Deployment configuration =='
docker compose config --quiet
bash k8s/validate.sh

echo 'Verification completed successfully.'
