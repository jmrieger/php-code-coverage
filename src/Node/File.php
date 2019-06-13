<?php declare(strict_types=1);
/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Node;

/**
 * Represents a file in the code coverage information tree.
 */
final class File extends AbstractNode
{
    /**
     * @var array
     */
    private $coverageData;

    /**
     * @var array
     */
    private $testData;

    /**
     * @var int
     */
    private $numExecutableLines = 0;

    /**
     * @var int
     */
    private $numExecutedLines = 0;

    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var array
     */
    private $traits = [];

    /**
     * @var array
     */
    private $functions = [];

    /**
     * @var array
     */
    private $branches = [];

    /**
     * @var array
     */
    private $paths = [];

    /**
     * @var array
     */
    private $linesOfCode = [];

    /**
     * @var int
     */
    private $numClasses;

    /**
     * @var int
     */
    private $numTestedClasses = 0;

    /**
     * @var int
     */
    private $numTraits;

    /**
     * @var int
     */
    private $numTestedTraits = 0;

    /**
     * @var int
     */
    private $numMethods;

    /**
     * @var int
     */
    private $numTestedMethods;

    /**
     * @var int
     */
    private $numTestedFunctions;

    /**
     * @var int
     */
    private $numPaths = 0;

    /**
     * @var int
     */
    private $numTestedPaths = 0;

    /**
     * @var int
     */
    private $numBranches = 0;

    /**
     * @var int
     */
    private $numTestedBranches = 0;

    /**
     * @var bool
     */
    private $cacheTokens;

    /**
     * @var array
     */
    private $codeUnitsByLine = [];

    public function __construct(string $name, AbstractNode $parent, array $coverageData, array $testData, bool $cacheTokens)
    {
        parent::__construct($name, $parent);

        $this->coverageData = $coverageData;
        $this->testData     = $testData;
        $this->cacheTokens  = $cacheTokens;

        $this->calculateStatistics();
    }

    /**
     * Returns the number of files in/under this node.
     */
    public function count(): int
    {
        return 1;
    }

    /**
     * Returns the code coverage data of this node.
     */
    public function getCoverageData(): array
    {
        return $this->coverageData;
    }

    /**
     * Returns the test data of this node.
     */
    public function getTestData(): array
    {
        return $this->testData;
    }

    /**
     * Returns the classes of this node.
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Returns the traits of this node.
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * Returns the functions of this node.
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * Returns the LOC/CLOC/NCLOC of this node.
     */
    public function getLinesOfCode(): array
    {
        return $this->linesOfCode;
    }

    /**
     * Returns the number of executable lines.
     */
    public function getNumExecutableLines(): int
    {
        return $this->numExecutableLines;
    }

    /**
     * Returns the number of executed lines.
     */
    public function getNumExecutedLines(): int
    {
        return $this->numExecutedLines;
    }

    /**
     * Returns the number of classes.
     */
    public function getNumClasses(): int
    {
        if ($this->numClasses === null) {
            $this->numClasses = 0;

            foreach ($this->classes as $class) {
                foreach ($class['methods'] as $method) {
                    if ($method['executableLines'] > 0) {
                        $this->numClasses++;

                        continue 2;
                    }
                }
            }
        }

        return $this->numClasses;
    }

    /**
     * Returns the number of tested classes.
     */
    public function getNumTestedClasses(): int
    {
        return $this->numTestedClasses;
    }

    /**
     * Returns the number of traits.
     */
    public function getNumTraits(): int
    {
        if ($this->numTraits === null) {
            $this->numTraits = 0;

            foreach ($this->traits as $trait) {
                foreach ($trait['methods'] as $method) {
                    if ($method['executableLines'] > 0) {
                        $this->numTraits++;

                        continue 2;
                    }
                }
            }
        }

        return $this->numTraits;
    }

    /**
     * Returns the number of tested traits.
     */
    public function getNumTestedTraits(): int
    {
        return $this->numTestedTraits;
    }

    /**
     * Returns the number of methods.
     */
    public function getNumMethods(): int
    {
        if ($this->numMethods === null) {
            $this->numMethods = 0;

            foreach ($this->classes as $class) {
                foreach ($class['methods'] as $method) {
                    if ($method['executableLines'] > 0) {
                        $this->numMethods++;
                    }
                }
            }

            foreach ($this->traits as $trait) {
                foreach ($trait['methods'] as $method) {
                    if ($method['executableLines'] > 0) {
                        $this->numMethods++;
                    }
                }
            }
        }

        return $this->numMethods;
    }

    /**
     * Returns the number of tested methods.
     */
    public function getNumTestedMethods(): int
    {
        if ($this->numTestedMethods === null) {
            $this->numTestedMethods = 0;

            foreach ($this->classes as $class) {
                foreach ($class['methods'] as $method) {
                    if ($method['executableLines'] > 0 &&
                        $method['coverage'] === 100) {
                        $this->numTestedMethods++;
                    }
                }
            }

            foreach ($this->traits as $trait) {
                foreach ($trait['methods'] as $method) {
                    if ($method['executableLines'] > 0 &&
                        $method['coverage'] === 100) {
                        $this->numTestedMethods++;
                    }
                }
            }
        }

        return $this->numTestedMethods;
    }

    /**
     * Returns the number of functions.
     */
    public function getNumFunctions(): int
    {
        return \count($this->functions);
    }

    /**
     * Returns the number of tested functions.
     */
    public function getNumTestedFunctions(): int
    {
        if ($this->numTestedFunctions === null) {
            $this->numTestedFunctions = 0;

            foreach ($this->functions as $function) {
                if ($function['executableLines'] > 0 &&
                    $function['coverage'] === 100) {
                    $this->numTestedFunctions++;
                }
            }
        }

        return $this->numTestedFunctions;
    }

    private function calculateStatistics(): void
    {
        if ($this->cacheTokens) {
            $tokens = \PHP_Token_Stream_CachingFactory::get($this->getPath());
        } else {
            $tokens = new \PHP_Token_Stream($this->getPath());
        }

        $this->linesOfCode = $tokens->getLinesOfCode();

        foreach (\range(1, $this->linesOfCode['loc']) as $lineNumber) {
            $this->codeUnitsByLine[$lineNumber] = [];
        }

        try {
            $this->processClasses($tokens);
            $this->processTraits($tokens);
            $this->processFunctions($tokens);
        } catch (\OutOfBoundsException $e) {
            // This can happen with PHP_Token_Stream if the file is syntactically invalid,
            // and probably affects a file that wasn't executed.
        }
        unset($tokens);

        foreach (\range(1, $this->linesOfCode['loc']) as $lineNumber) {
            // Check to see if we've identified this line as executed, not executed, or not executable
            if (\array_key_exists($lineNumber, $this->coverageData['lines'])) {
                // If the element is null, that indicates this line is not executable
                if ($this->coverageData['lines'][$lineNumber] !== null) {
                    foreach ($this->codeUnitsByLine[$lineNumber] as &$codeUnit) {
                        $codeUnit['executableLines']++;
                    }

                    unset($codeUnit);

                    $this->numExecutableLines++;
                }


                if ($this->coverageData['lines'][$lineNumber]['pathCovered'] === true) {
                    foreach ($this->codeUnitsByLine[$lineNumber] as &$codeUnit) {
                        $codeUnit['executedLines']++;
                    }

                    unset($codeUnit);

                    $this->numExecutedLines++;
                }
            }
        }

        foreach ($this->traits as &$trait) {
            $this->calcAndApplyClassAggregate($trait, $trait['traitName'], $this->numTestedTraits);
        }

        unset($trait);

        foreach ($this->classes as &$class) {
            $this->calcAndApplyClassAggregate($class, $class['className'], $this->numTestedClasses);
        }

        unset($class);

        foreach ($this->functions as &$function) {
            if ($function['executableLines'] > 0) {
                $function['coverage'] = ($function['executedLines'] /
                        $function['executableLines']) * 100;
            } else {
                $function['coverage'] = 100;
            }

            if ($function['coverage'] === 100) {
                $this->numTestedFunctions++;
            }

            $function['crap'] = $this->crap(
                $function['ccn'],
                $function['coverage']
            );
        }

        unset($function);

        // Process Path Coverage for non-class functions
        foreach ($this->functions as &$function) {
            if (isset($this->coverageData['paths'][$function['functionName']])) {
                $functionPaths = $this->coverageData['paths'][$function['functionName']];

                $this->calculatePathsAggregate($functionPaths, $numExecutablePaths, $numExecutedPaths);

                $function['executablePaths'] = $numExecutablePaths;
                $this->numPaths += $numExecutablePaths;

                $function['executedPaths'] = $numExecutedPaths;
                $this->numTestedPaths += $numExecutablePaths;
            }

            if (isset($this->coverageData['branches'][$function['functionName']])) {
                $functionBranches = $this->coverageData['branches'][$function['functionName']];

                $this->calculatePathsAggregate($functionBranches, $numExecutableBranches, $numExecutedBranches);

                $function['executableBranches'] = $numExecutableBranches;
                $this->numBranches += $numExecutableBranches;

                $function['executedBranches'] = $numExecutedBranches;
                $this->numTestedBranches += $numExecutedBranches;
            }
        }
    }


    /**
     * @param array $classOrTrait
     * @param string $classOrTraitName
     * @param int $numTestedClassOrTrait
     */
    private function calcAndApplyClassAggregate(
        array &$classOrTrait,
        string $classOrTraitName,
        int &$numTestedClassOrTrait
    ): void {
        foreach ($classOrTrait['methods'] as &$method) {
            $methodName = $method['methodName'];

            if ($method['executableLines'] > 0) {
                $method['coverage'] = ($method['executedLines'] / $method['executableLines']) * 100;
            } else {
                $method['coverage'] = 100;
            }

            $method['crap'] = $this->crap(
                $method['ccn'],
                $method['coverage']
            );

            $classOrTrait['ccn'] += $method['ccn'];

            if (isset($this->coverageData['paths'])) {
                $methodCoveragePath = $methodName;

                // @todo - Might not need this anonymous function handling...
                if ($methodName === 'anonymous function') {
                    foreach ($this->coverageData['paths'] as $index => $path) {
                        if ($method['startLine'] === $path[0]['line_start']) {
                            $methodCoveragePath = $index;
                        }
                    }
                }

                $methodCoveragePath = $classOrTraitName . '->' . $methodCoveragePath;

                if (isset($this->coverageData['paths'][$methodCoveragePath])) {
                    $methodPaths = $this->coverageData['paths'][$methodCoveragePath];
                    $this->calculatePathsAggregate($methodPaths, $numExecutablePaths, $numExexutedPaths);

                    $method['executablePaths'] = $numExecutablePaths;
                    $classOrTrait['executablePaths'] += $numExecutablePaths;
                    $this->numPaths += $numExecutablePaths;

                    $method['executedPaths'] = $numExexutedPaths;
                    $classOrTrait['executedPaths'] += $numExexutedPaths;
                    $this->numTestedPaths += $numExexutedPaths;
                }

            }

            if (isset($this->coverageData['branches'])) {
                $methodCoverageBranch = $methodName;

                // @todo - Might not need this anonymous function handling...
                if ($methodName === 'anonymous function') {
                    foreach ($this->coverageData['branches'] as $index => $branch) {
                        if ($method['startLine'] === $branch[0]['line_start']) {
                            $methodCoverageBranch = $index;
                        }
                    }
                }

                $methodCoverageBranch = $classOrTraitName . '->' . $methodCoverageBranch;

                if (isset($this->coverageData['branches'][$methodCoverageBranch])) {
                    $methodPaths = $this->coverageData['branches'][$methodCoverageBranch];
                    $this->calculatePathsAggregate($methodPaths, $numExecutableBranches, $numExexutedBranches);

                    $method['executableBranches'] = $numExecutableBranches;
                    $classOrTrait['executableBranches'] += $numExecutableBranches;
                    $this->numBranches += $numExecutableBranches;

                    $method['executedBranches'] = $numExexutedBranches;
                    $classOrTrait['executedBranches'] += $numExexutedBranches;
                    $this->numTestedBranches += $numExexutedBranches;
                }
            }
        }
        unset($method);

        if ($classOrTrait['executableLines'] > 0) {
            $classOrTrait['coverage'] = ($classOrTrait['executedLines'] /
                    $classOrTrait['executableLines']) * 100;

            if ($classOrTrait['coverage'] === 100) {
                $numTestedClassOrTrait++;
            }
        } else {
            $classOrTrait['coverage'] = 100;
        }

        $classOrTrait['crap'] = $this->crap(
            $classOrTrait['ccn'],
            $classOrTrait['coverage']
        );
    }

    private function calculatePathsAggregate(array $paths, &$functionExecutablePaths, &$functionExecutedPaths): void
    {
        $functionExecutablePaths = \count($paths);

        $functionExecutedPaths = \array_reduce(
            $paths,
            static function ($carry, $value) {
                return ($value['hit'] > 0) ? $carry + 1 : $carry;
            },
            0
        );
    }

    private function processClasses(\PHP_Token_Stream $tokens): void
    {
        $classes = $tokens->getClasses();
        $link    = $this->getId() . '.html#';

        foreach ($classes as $className => $class) {
            if (\strpos($className, 'anonymous') === 0) {
                continue;
            }

            if (!empty($class['package']['namespace'])) {
                $className = $class['package']['namespace'] . '\\' . $className;
            }

            $this->classes[$className] = [
                'className'          => $className,
                'methods'            => [],
                'startLine'          => $class['startLine'],
                'executableLines'    => 0,
                'executedLines'      => 0,
                'executablePaths'    => 0,
                'executedPaths'      => 0,
                'executableBranches' => 0,
                'executedBranches'   => 0,
                'ccn'                => 0,
                'coverage'           => 0,
                'crap'               => 0,
                'package'            => $class['package'],
                'link'               => $link . $class['startLine'],
            ];

            foreach ($class['methods'] as $methodName => $method) {
                if (\strpos($methodName, 'anonymous') === 0) {
                    continue;
                }

                $this->classes[$className]['methods'][$methodName] = $this->newMethod($methodName, $method, $link);

                foreach (\range($method['startLine'], $method['endLine']) as $lineNumber) {
                    $this->codeUnitsByLine[$lineNumber] = [
                        &$this->classes[$className],
                        &$this->classes[$className]['methods'][$methodName],
                    ];
                }
            }
        }
    }

    private function processTraits(\PHP_Token_Stream $tokens): void
    {
        $traits = $tokens->getTraits();
        $link   = $this->getId() . '.html#';

        foreach ($traits as $traitName => $trait) {
            $this->traits[$traitName] = [
                'traitName'          => $traitName,
                'methods'            => [],
                'startLine'          => $trait['startLine'],
                'executableLines'    => 0,
                'executedLines'      => 0,
                'executablePaths'    => 0,
                'executedPaths'      => 0,
                'executableBranches' => 0,
                'executedBranches'   => 0,
                'ccn'                => 0,
                'coverage'           => 0,
                'crap'               => 0,
                'package'            => $trait['package'],
                'link'               => $link . $trait['startLine'],
            ];

            foreach ($trait['methods'] as $methodName => $method) {
                if (\strpos($methodName, 'anonymous') === 0) {
                    continue;
                }

                $this->traits[$traitName]['methods'][$methodName] = $this->newMethod($methodName, $method, $link);

                foreach (\range($method['startLine'], $method['endLine']) as $lineNumber) {
                    $this->codeUnitsByLine[$lineNumber] = [
                        &$this->traits[$traitName],
                        &$this->traits[$traitName]['methods'][$methodName],
                    ];
                }
            }
        }
    }

    private function processFunctions(\PHP_Token_Stream $tokens): void
    {
        $functions = $tokens->getFunctions();
        $link      = $this->getId() . '.html#';

        foreach ($functions as $functionName => $function) {
            if (\strpos($functionName, 'anonymous') === 0) {
                continue;
            }

            $this->functions[$functionName] = [
                'functionName'       => $functionName,
                'signature'          => $function['signature'],
                'startLine'          => $function['startLine'],
                'executableLines'    => 0,
                'executedLines'      => 0,
                'executablePaths'    => 0,
                'executedPaths'      => 0,
                'executableBranches' => 0,
                'executedBranches'   => 0,
                'ccn'                => $function['ccn'],
                'coverage'           => 0,
                'crap'               => 0,
                'link'               => $link . $function['startLine'],
            ];

            foreach (\range($function['startLine'], $function['endLine']) as $lineNumber) {
                $this->codeUnitsByLine[$lineNumber] = [&$this->functions[$functionName]];
            }
        }
    }

    private function crap(int $ccn, float $coverage): string
    {
        if ($coverage === 0) {
            return (string) ($ccn ** 2 + $ccn);
        }

        if ($coverage >= 95) {
            return (string) $ccn;
        }

        return \sprintf(
            '%01.2F',
            $ccn ** 2 * (1 - $coverage / 100) ** 3 + $ccn
        );
    }

    private function newMethod(string $methodName, array $method, string $link): array
    {
        return [
            'methodName'         => $methodName,
            'visibility'         => $method['visibility'],
            'signature'          => $method['signature'],
            'startLine'          => $method['startLine'],
            'endLine'            => $method['endLine'],
            'executableLines'    => 0,
            'executedLines'      => 0,
            'executablePaths'    => 0,
            'executedPaths'      => 0,
            'executableBranches' => 0,
            'executedBranches'   => 0,
            'ccn'                => $method['ccn'],
            'coverage'           => 0,
            'crap'               => 0,
            'link'               => $link . $method['startLine'],
        ];
    }

    /**
     * Returns the paths of this node.
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Returns the branches of this node.
     */
    public function getBranches(): array
    {
        return $this->branches;
    }

    /**
     * Returns the number of paths.
     */
    public function getNumPaths(): int
    {
        return $this->numPaths;
    }

    /**
     * Returns the number of tested paths.
     */
    public function getNumTestedPaths(): int
    {
        return $this->numTestedPaths;
    }

    /**
     * Returns the number of branches.
     */
    public function getNumBranches(): int
    {
        return $this->numBranches;
    }

    /**
     * Returns the number of tested branches.
     */
    public function getNumTestedBranches(): int
    {
        return $this->numTestedBranches;
    }
}
