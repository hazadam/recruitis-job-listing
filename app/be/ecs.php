<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withCache('var/cache/ecs')
    ->withSets([SetList::PSR_12])
    ->withRules([
        ConcatSpaceFixer::class,
        NoSuperfluousPhpdocTagsFixer::class,
        NoEmptyPhpdocFixer::class,
        NoExtraBlankLinesFixer::class,
        DeclareEqualNormalizeFixer::class,
        ReturnTypeDeclarationFixer::class,
        NoUnusedImportsFixer::class,
        DeclareStrictTypesFixer::class
    ])
    ->withConfiguredRule(ArraySyntaxFixer::class, ['syntax' => 'short'])
    ->withConfiguredRule(ReferenceUsedNamesOnlySniff::class, [
        'allowFullyQualifiedGlobalFunctions' => false,
        'allowFullyQualifiedGlobalConstants' => false,
        'allowFallbackGlobalFunctions' => false,
        'allowFullyQualifiedGlobalClasses' => false,
        'allowPartialUses' => false,
    ])
    ->withParallel(300, 8);
