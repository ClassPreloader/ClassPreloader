<?php

return ClassPreloader\ClassLoader::getIncludes(function (ClassPreloader\ClassLoader $loader) {
    $loader->register();
    new Foo();
});
