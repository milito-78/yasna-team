<?php

return [
    "appends" => [
        "success" => true,
        "status_code" => false
    ],

    /*
     * Fields name. you can change fields name from here.
     * Like "data" => "result"
    */
    "fields" => [
        "message"   => "message",
        "success"   => "success",
        "code"      => "code",
        "data"      => "data",
        "error"     => "error",
        "errors"    => "errors",
    ],

    /*
     * Fields orders. you can change fields order from here.
     * Like "data" => 51
    */
    "ordered" => [
        "message"   => 10,
        "success"   => 20,
        "code"      => 30,
        "data"      => 40,
        "error"     => 50,
        "errors"    => 60,
    ],

    /*
     * Errors as array
     */
    "array_errors" => true
];
