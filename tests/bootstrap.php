<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load WordPress stubs first (provides WordPress functions)
require_once __DIR__ . '/../vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';
require_once __DIR__ . '/../vendor/php-stubs/wp-cli-stubs/wp-cli-stubs.php';

// EDD core stubs (transitive dep — ConvertKit references EDD core types)
require_once __DIR__ . '/../vendor/arts/easy-digital-downloads-stubs/easy-digital-downloads-stubs.php';

// EDD ConvertKit stubs
require_once __DIR__ . '/../edd-convertkit-stubs.php';
