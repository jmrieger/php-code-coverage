<?php declare(strict_types=1);
/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Html;

use SebastianBergmann\CodeCoverage\Node\AbstractNode as Node;
use SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;

/**
 * Renders a directory node.
 */
final class Directory extends Renderer
{
    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function render(DirectoryNode $node, string $file): void
    {
        $templateName = $this->templatePath . 'directory.html';
        if ($this->determineBranchCoverage) {
            $templateName = $this->templatePath . 'directory_branch.html';
        }

        $template = new \Text_Template($templateName, '{{', '}}');

        $this->setCommonTemplateVariables($template, $node);

        $items = $this->renderItem($node, true);

        foreach ($node->getDirectories() as $item) {
            $items .= $this->renderItem($item);
        }

        foreach ($node->getFiles() as $item) {
            $items .= $this->renderItem($item);
        }

        $template->setVar(
            [
                'id'    => $node->getId(),
                'items' => $items,
            ]
        );

        $template->renderTo($file);
    }

    private function renderItem(Node $node, bool $total = false): string
    {
        $data = [
            'numClasses'                    => $node->getNumClassesAndTraits(),
            'numTestedClasses'              => $node->getNumTestedClassesAndTraits(),
            'numMethods'                    => $node->getNumFunctionsAndMethods(),
            'numTestedMethods'              => $node->getNumTestedFunctionsAndMethods(),
            'linesExecutedPercent'          => $node->getLineExecutedPercent(false),
            'linesExecutedPercentAsString'  => $node->getLineExecutedPercent(),
            'numExecutedLines'              => $node->getNumExecutedLines(),
            'numExecutableLines'            => $node->getNumExecutableLines(),
            'testedMethodsPercent'          => $node->getTestedFunctionsAndMethodsPercent(false),
            'testedMethodsPercentAsString'  => $node->getTestedFunctionsAndMethodsPercent(),
            'testedClassesPercent'          => $node->getTestedClassesAndTraitsPercent(false),
            'testedClassesPercentAsString'  => $node->getTestedClassesAndTraitsPercent(),
            'testedBranchesPercent'         => $node->getTestedBranchesPercent(false),
            'testedBranchesPercentAsString' => $node->getTestedBranchesPercent(),
            'testedPathsPercent'            => $node->getTestedPathsPercent(false),
            'testedPathsPercentAsString'    => $node->getTestedPathsPercent(),
            'numExecutablePaths'            => $node->getNumPaths(),
            'numExecutedPaths'              => $node->getNumTestedPaths(),
            'numExecutableBranches'         => $node->getNumBranches(),
            'numExecutedBranches'           => $node->getNumTestedBranches(),
        ];

        if ($total) {
            $data['name'] = 'Total';
        } else {
            if ($node instanceof DirectoryNode) {
                $data['name'] = \sprintf(
                    '<a href="%s/index.html">%s</a>',
                    $node->getName(),
                    $node->getName()
                );

                $up = \str_repeat('../', \count($node->getPathAsArray()) - 2);

                $data['icon'] = \sprintf('<img src="%s_icons/file-directory.svg" class="octicon" />', $up);
            } else {
                $data['name'] = \sprintf(
                    '<a href="%s.html">%s</a>',
                    $node->getName(),
                    $node->getName()
                );

                $up = \str_repeat('../', \count($node->getPathAsArray()) - 2);

                $data['icon'] = \sprintf('<img src="%s_icons/file-code.svg" class="octicon" />', $up);
            }
        }

        $templateName = $this->templatePath . 'directory_item.html';
        if ($this->determineBranchCoverage) {
            $templateName = $this->templatePath . 'directory_item_branch.html';
        }

        return $this->renderItemTemplate(
            new \Text_Template($templateName, '{{', '}}'),
            $data
        );
    }
}
