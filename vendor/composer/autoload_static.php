<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2748e8162f6526e33e431075b320a662
{
    public static $prefixLengthsPsr4 = array (
        'i' => 
        array (
            'iutnc\\netvod\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'iutnc\\netvod\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2748e8162f6526e33e431075b320a662::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2748e8162f6526e33e431075b320a662::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2748e8162f6526e33e431075b320a662::$classMap;

        }, null, ClassLoader::class);
    }
}
