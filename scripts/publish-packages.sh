#!/usr/bin/env bash

set -euo pipefail

: "${PACKAGIST_USERNAME:?Set PACKAGIST_USERNAME to the approved Packagist account}"
: "${PACKAGIST_TOKEN:?Set PACKAGIST_TOKEN through an environment secret}"

packagist_api='https://packagist.org/api/submit'

for package_manifest in modules/*/composer.json; do
    package_name=$(jq -r '.name' "$package_manifest")
    repository_name=${package_name#liberusoftware/}
    repository_url="https://github.com/liberusoftware/${repository_name}"

    payload=$(jq -n \
        --arg username "$PACKAGIST_USERNAME" \
        --arg api_token "$PACKAGIST_TOKEN" \
        --arg repository_url "$repository_url" \
        '{username: $username, apiToken: $api_token, repository: {url: $repository_url}}')

    response=$(curl --fail-with-body --silent --show-error \
        --request POST \
        --header 'Content-Type: application/json' \
        --data "$payload" \
        "$packagist_api")

    echo "$package_name: $response"
done
