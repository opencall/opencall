<?php

if (file_exists(__DIR__ . '/../../vendor/.composer/autoload.php')) {
    include_once __DIR__ . '/../../vendor/.composer/autoload.php'; 
} else {
    $classLoaderFile = __DIR__ . '/../../vendor/ClassLoader/UniversalClassLoader.php';
    if (file_exists($classLoaderFile)) {
        include_once $classLoaderFile;
    } else {
        throw new Exception('Missing Symfony ClassLoader ' . $filename);
    }

    $loader = new Symfony\Component\ClassLoader\UniversalClassLoader();

    $loader->registerNamespace('FuseSource', __DIR__ . '/../main');
    $loader->register();
}
        