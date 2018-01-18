<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit547f1ba8138a17d6b867624279d0c7eb
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\composer\\' => 15,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPExcel' => 
            array (
                0 => __DIR__ . '/..' . '/phpoffice/phpexcel/Classes',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit547f1ba8138a17d6b867624279d0c7eb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit547f1ba8138a17d6b867624279d0c7eb::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit547f1ba8138a17d6b867624279d0c7eb::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
