<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

use PHPWell\PHPGenerator\PHPGenerator;

try {
    return (new PHPGenerator())
        ->loadConfiguration(__DIR__ . '/configuration.neon')
        ->setBlueprintsPath(__DIR__ . '/blueprints/')
        ->setOutputDirectory(__DIR__ . '/generated/')
        ->init()
    ;
} catch (Exception $e) {
    return sprintf('PHPGeneratorError: %s', $e->getMessage());
}