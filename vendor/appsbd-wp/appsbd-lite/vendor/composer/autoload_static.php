<?php


namespace Composer\Autoload;

class ComposerStaticInit3677229d0bf6a7fbf6d1afe1470ab85a
{
    public static $files = array (
        'a19e8cd3aa4160abcc3f6edf7cd368e2' => __DIR__ . '/../..' . '/appsbd_lite/v2/core/class-kernel-lite.php',
        'c2ee1676bdaff559695ea41431ae0b67' => __DIR__ . '/../..' . '/appsbd_lite/v2/helper/base-helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Appsbd_Lite\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Appsbd_Lite\\' => 
        array (
            0 => __DIR__ . '/../..' . '/appsbd_lite',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3677229d0bf6a7fbf6d1afe1470ab85a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3677229d0bf6a7fbf6d1afe1470ab85a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3677229d0bf6a7fbf6d1afe1470ab85a::$classMap;

        }, null, ClassLoader::class);
    }
}
