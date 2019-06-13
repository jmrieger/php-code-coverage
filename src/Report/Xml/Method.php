<?php declare(strict_types=1);
/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Xml;

final class Method
{
    /**
     * @var \DOMElement
     */
    private $contextNode;

    public function __construct(\DOMElement $context, string $name)
    {
        $this->contextNode = $context;

        $this->setName($name);
    }

    public function setSignature(string $signature): void
    {
        $this->contextNode->setAttribute('signature', $signature);
    }

    public function setLines(string $start, ?string $end = null): void
    {
        $this->contextNode->setAttribute('start', $start);

        if ($end !== null) {
            $this->contextNode->setAttribute('end', $end);
        }
    }

    public function setTotals(
        string $executableLines,
        string $executedLines,
        string $coverage
    ): void {
        $this->contextNode->setAttribute('executable', $executableLines);
        $this->contextNode->setAttribute('executed', $executedLines);
        $this->contextNode->setAttribute('coverage', $coverage);
    }

    public function setPathTotals(string $executablePaths, string $executedPaths): void
    {
        $this->contextNode->setAttribute('executablePaths', $executablePaths);
        $this->contextNode->setAttribute('executedPaths', $executedPaths);
    }

    public function setBranchTotals(string $executableBranches, string $executedBranches): void
    {
        $this->contextNode->setAttribute('executableBranches', $executableBranches);
        $this->contextNode->setAttribute('executedBranches', $executedBranches);
    }

    public function setCrap(string $crap): void
    {
        $this->contextNode->setAttribute('crap', $crap);
    }

    private function setName(string $name): void
    {
        $this->contextNode->setAttribute('name', $name);
    }
}
