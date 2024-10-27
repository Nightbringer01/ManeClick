<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3cf13107d6e604a8c658c21fcda5208c
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3cf13107d6e604a8c658c21fcda5208c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3cf13107d6e604a8c658c21fcda5208c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3cf13107d6e604a8c658c21fcda5208c::$classMap;

        }, null, ClassLoader::class);
    }
}