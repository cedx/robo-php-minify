#!/usr/bin/env pwsh
Set-StrictMode -Version Latest
Set-Location (Split-Path $PSScriptRoot)

php -l example/RoboFile.php
vendor/bin/phpstan analyse --configuration=etc/phpstan.neon
