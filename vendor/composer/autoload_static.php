<?php


namespace Composer\Autoload;

class ComposerStaticInitff69e1be6b8f8408dc1ffea1cbf21e4a
{
    public static $files = array (
        'a19e8cd3aa4160abcc3f6edf7cd368e2' => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v2/core/class-kernel-lite.php',
        'c2ee1676bdaff559695ea41431ae0b67' => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v2/helper/base-helper.php',
        'd54297b4fb1177761fb42a6db6667275' => __DIR__ . '/../..' . '/minicart_lite/core/class-minicart-lite.php',
        '0b8dde6c76ada4f32d853c6417a1a85e' => __DIR__ . '/../..' . '/minicart_lite/helper/global-helper.php',
        '87096bd7f2f0229855cb51c165d63069' => __DIR__ . '/../..' . '/minicart_lite/helper/plugin-helper.php',
        '4b276f3584d4874d14b81bed3094c0d4' => __DIR__ . '/../..' . '/minicart_lite/libs/class-minicart-addons.php',
        'da2328aa5c4563ce0d71a22da9b83d51' => __DIR__ . '/../..' . '/minicart_lite/libs/class-minicart-loader.php',
    );

    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MiniCart_lite\\' => 14,
        ),
        'A' => 
        array (
            'Appsbd_Lite\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MiniCart_lite\\' => 
        array (
            0 => __DIR__ . '/../..' . '/minicart_lite',
        ),
        'Appsbd_Lite\\' => 
        array (
            0 => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitff69e1be6b8f8408dc1ffea1cbf21e4a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitff69e1be6b8f8408dc1ffea1cbf21e4a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitff69e1be6b8f8408dc1ffea1cbf21e4a::$classMap;

        }, null, ClassLoader::class);
    }
}
