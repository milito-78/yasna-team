<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function arrays_are_similar(array $array, array $similar): bool
    {

        if (count(array_diff_assoc($array, $similar))) {
            return false;
        }

        foreach($array as $k => $v) {
            if ($v !== $similar[$k]) {
                return false;
            }
        }

        return true;
    }
}
