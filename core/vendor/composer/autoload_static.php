<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc57fe010032568a67499f5b2880f316d
{
    public static $files = array (
        '5255c38a0faeba867671b61dfda6d864' => __DIR__ . '/..' . '/paragonie/random_compat/lib/random.php',
        '72579e7bd17821bb1321b87411366eae' => __DIR__ . '/..' . '/illuminate/support/helpers.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '2a1181a15c0b875073a40ff3b11f1688' => __DIR__ . '/../..' . '/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Webuni\\FrontMatter\\' => 19,
        ),
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Yaml\\' => 23,
            'Symfony\\Component\\Finder\\' => 25,
        ),
        'M' => 
        array (
            'Mimey\\' => 6,
            'Michelf\\' => 8,
            'Matomo\\Ini\\' => 11,
        ),
        'I' => 
        array (
            'Illuminate\\Support\\' => 19,
            'Illuminate\\Filesystem\\' => 22,
            'Illuminate\\Contracts\\' => 21,
        ),
        'D' => 
        array (
            'Doctrine\\Inflector\\' => 19,
            'Doctrine\\Common\\Inflector\\' => 26,
        ),
        'A' => 
        array (
            'AKlump\\LoftLib\\' => 15,
            'AKlump\\Data\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Webuni\\FrontMatter\\' => 
        array (
            0 => __DIR__ . '/..' . '/webuni/front-matter/src',
        ),
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'Symfony\\Component\\Finder\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/finder',
        ),
        'Mimey\\' => 
        array (
            0 => __DIR__ . '/..' . '/ralouphie/mimey/src',
        ),
        'Michelf\\' => 
        array (
            0 => __DIR__ . '/..' . '/michelf/php-markdown/Michelf',
        ),
        'Matomo\\Ini\\' => 
        array (
            0 => __DIR__ . '/..' . '/matomo/ini/src',
        ),
        'Illuminate\\Support\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/support',
        ),
        'Illuminate\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/filesystem',
        ),
        'Illuminate\\Contracts\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/contracts',
        ),
        'Doctrine\\Inflector\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/inflector/lib/Doctrine/Inflector',
        ),
        'Doctrine\\Common\\Inflector\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/inflector/lib/Doctrine/Common/Inflector',
        ),
        'AKlump\\LoftLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/aklump/loft-lib/src/AKlump/LoftLib',
        ),
        'AKlump\\Data\\' => 
        array (
            0 => __DIR__ . '/..' . '/aklump/data',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
        'J' => 
        array (
            'JasonLewis\\ResourceWatcher' => 
            array (
                0 => __DIR__ . '/..' . '/jasonlewis/resource-watcher/src',
            ),
        ),
        'A' => 
        array (
            'AKlump\\LoftDocs\\' => 
            array (
                0 => __DIR__ . '/../..' . '/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'aklump\\kit_php\\Compiler' => __DIR__ . '/..' . '/aklump/kit_php/classes/Compiler.php',
        'aklump\\kit_php\\CompilerInterface' => __DIR__ . '/..' . '/aklump/kit_php/classes/Compiler.php',
        'aklump\\kit_php\\Imports' => __DIR__ . '/..' . '/aklump/kit_php/classes/Imports.php',
        'aklump\\kit_php\\ImportsInterface' => __DIR__ . '/..' . '/aklump/kit_php/classes/Imports.php',
        'aklump\\kit_php\\Kit' => __DIR__ . '/..' . '/aklump/kit_php/classes/Kit.php',
        'aklump\\kit_php\\KitInterface' => __DIR__ . '/..' . '/aklump/kit_php/classes/Kit.php',
        'aklump\\kit_php\\Variables' => __DIR__ . '/..' . '/aklump/kit_php/classes/Variables.php',
        'aklump\\kit_php\\VariablesInterface' => __DIR__ . '/..' . '/aklump/kit_php/classes/Variables.php',
        'aklump\\loft_parser\\HRParseAction' => __DIR__ . '/..' . '/aklump/loft_parser/classes/ParseAction.php',
        'aklump\\loft_parser\\HTMLTagParseAction' => __DIR__ . '/..' . '/aklump/loft_parser/classes/ParseAction.php',
        'aklump\\loft_parser\\HTMLTagRemoveAction' => __DIR__ . '/..' . '/aklump/loft_parser/classes/ParseAction.php',
        'aklump\\loft_parser\\LinkParseAction' => __DIR__ . '/..' . '/aklump/loft_parser/classes/ParseAction.php',
        'aklump\\loft_parser\\ListParseAction' => __DIR__ . '/..' . '/aklump/loft_parser/classes/ParseAction.php',
        'aklump\\loft_parser\\MediaWikiParser' => __DIR__ . '/..' . '/aklump/loft_parser/classes/MediaWikiParser.php',
        'aklump\\loft_parser\\ParseAction' => __DIR__ . '/..' . '/aklump/loft_parser/classes/ParseAction.php',
        'aklump\\loft_parser\\ParseActionInterface' => __DIR__ . '/..' . '/aklump/loft_parser/classes/ParseAction.php',
        'aklump\\loft_parser\\Parser' => __DIR__ . '/..' . '/aklump/loft_parser/classes/Parser.php',
        'aklump\\loft_parser\\ParserInterface' => __DIR__ . '/..' . '/aklump/loft_parser/classes/Parser.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc57fe010032568a67499f5b2880f316d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc57fe010032568a67499f5b2880f316d::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitc57fe010032568a67499f5b2880f316d::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitc57fe010032568a67499f5b2880f316d::$classMap;

        }, null, ClassLoader::class);
    }
}
