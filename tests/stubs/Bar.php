<?php

use Tests\Stubs\Baz;

class Bar
{
    public function qwerty()
    {
        $foo = new Baz();
        // this comment should be removed
        return __DIR__;
    }
}
