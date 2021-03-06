<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcb7f7574863387b59d666a3edd408bdc
{
    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'app\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'app\\App' => __DIR__ . '/../..' . '/src/App.php',
        'app\\JiraHandler' => __DIR__ . '/../..' . '/src/JiraHandler.php',
        'app\\OptionHandler' => __DIR__ . '/../..' . '/src/OptionHandler.php',
        'app\\posttypes\\AbstractPostType' => __DIR__ . '/../..' . '/src/posttypes/AbstractPostType.php',
        'app\\posttypes\\EventPostType' => __DIR__ . '/../..' . '/src/posttypes/EventPostType.php',
        'app\\posttypes\\SocialOrganisationPostType' => __DIR__ . '/../..' . '/src/posttypes/SocialOrgansiationPostType.php',
        'app\\posttypes\\SponsorPostType' => __DIR__ . '/../..' . '/src/posttypes/SponsorPostType.php',
        'app\\posttypes\\WichtelTypePostType' => __DIR__ . '/../..' . '/src/posttypes/WichtelTypePostType.php',
        'app\\posttypes\\WishPostType' => __DIR__ . '/../..' . '/src/posttypes/WishPostType.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcb7f7574863387b59d666a3edd408bdc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcb7f7574863387b59d666a3edd408bdc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcb7f7574863387b59d666a3edd408bdc::$classMap;

        }, null, ClassLoader::class);
    }
}
