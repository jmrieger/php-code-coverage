<?php declare(strict_types=1);
/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

use SebastianBergmann\CodeCoverage\RuntimeException;

/**
 * Driver for PCOV code coverage functionality.
 *
 * @codeCoverageIgnore
 */
final class PCOV implements Driver
{
    /**
     * Specify that branch coverage should be included with collected code coverage information.
     */
    public function setDetermineBranchCoverage(bool $flag): void
    {
        throw new RuntimeException('Branch coverage is not supported in PHPDBG');
    }

    /**
     * Start collection of code coverage information.
     */
    public function start(bool $determineUnusedAndDead = true): void
    {
        \pcov\start();
    }

    /**
     * Stop collection of code coverage information.
     */
    public function stop(): array
    {
        \pcov\stop();

        $waiting = \pcov\waiting();
        $collect = [];

        if ($waiting) {
            $collect = \pcov\collect(\pcov\inclusive, $waiting);

            \pcov\clear();
        }

        return $collect;
    }
}
