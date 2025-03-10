<?php

/*
 * Nytris Turbo - Turbocharged HTTP/FastCGI for PHP.
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/nytris/turbo/
 *
 * Released under the MIT license.
 * https://github.com/nytris/turbo/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

//use Nytris\Boot\BootConfig;
//use Nytris\Boot\PlatformConfig;
//use Nytris\Nytris;
//use Tasque\Core\Scheduler\ContextSwitch\PromiscuousStrategy;
//use Tasque\EventLoop\TasqueEventLoopPackage;
//use Tasque\TasquePackage;

use Asmblah\PhpCodeShift\CodeShift;
use Asmblah\PhpCodeShift\Shifter\Shift\Shift\ShiftTypeInterface;

require_once __DIR__ . '/../vendor/autoload.php';

Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
Mockery::globalHelpers();

//$bootConfig = new BootConfig(new PlatformConfig(dirname(__DIR__) . '/var/nytris/'));
//$bootConfig->installPackage(new TasquePackage(
//    schedulerStrategy: new PromiscuousStrategy(),
//    preemptive: false,
//));
//$bootConfig->installPackage(new TasqueEventLoopPackage());
//
//Nytris::boot($bootConfig);
//
//\Tasque\Tasque::excludeComposerPackage('sebastian/recursion-context');
////\Tasque\Tasque::excludeComposerPackage('phpunit/phpunit');
////\Tasque\Tasque::excludeComposerPackage('phpunit/php-timer');
//\Tasque\Tasque::excludeComposerPackage('mockery/mockery');

//$codeShift = new CodeShift();
//$codeShift->registerShiftType(
//    new class implements ShiftTypeInterface {
//        public function getName(): string
//        {
//            return 'nytris';
//        }
//
//        public function getDescription(): string
//        {
//            return 'Nytris Turbo';
//        }
//    }
//);
