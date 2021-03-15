<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\Core\Configuration\Option;
use Rector\DeadCode\Rector\Class_\RemoveUnusedDoctrineEntityMethodAndPropertyRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Privatization\Rector\Class_\ChangeReadOnlyVariableWithDefaultValueToConstantRector;
use Rector\Privatization\Rector\Class_\RepeatedLiteralToClassConstantRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;


return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    // paths to refactor; solid alternative to CLI arguments
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // symfony container path
    $parameters->set(
        Option::SYMFONY_CONTAINER_XML_PATH_PARAMETER,
        __DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml'
    );

    // autoload path
    $parameters->set(
        Option::AUTOLOAD_PATHS,
        __DIR__ . '/vendor/bin/.phpunit/phpunit-8.5-0/vendor/autoload.php'
    );

    // Define what rule sets will be applied
    $parameters->set(Option::SETS, [
        SetList::DEAD_CODE,
        SetList::PHP_74,
        SetList::PSR_4,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::PHPUNIT_CODE_QUALITY,
        SetList::SYMFONY_52,
        SetList::SYMFONY_CODE_QUALITY,
    ]);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);

    // Define what paths and/or rules must be skipped
    $parameters->set(Option::SKIP, [
        // paths to skip
        __DIR__ . '/src/Kernel.php',
        __DIR__ . '/src/DataFixtures/*',

        // rules to skip
        RepeatedLiteralToClassConstantRector::class,
        RemoveUnusedDoctrineEntityMethodAndPropertyRector::class,
        NewlineBeforeNewAssignSetRector::class,
        ChangeReadOnlyVariableWithDefaultValueToConstantRector::class,
        CompleteDynamicPropertiesRector::class,
        TypedPropertyRector::class,
    ]);
};
