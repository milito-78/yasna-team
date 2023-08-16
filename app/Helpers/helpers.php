<?php

if (!function_exists("success_json")){
    function success_json(): \Milito\ResponseGenerator\States\Success\SuccessState{
        return \Milito\ResponseGenerator\Facades\MilitoResponseGenerator::success();
    }
}

if (!function_exists("failed_json")){
    function failed_json(): \Milito\ResponseGenerator\States\Failed\FailedState {
        return \Milito\ResponseGenerator\Facades\MilitoResponseGenerator::failed();
    }
}


if (!function_exists("logError")){
    function logError($error , string $message = "" , array $data  = [] , string $step = ""): void
    {
        $step = $step != "" ? $step : "#1";
        $message = $step . " "  . ($message != "" ? $message : "Error : " .$error->getMessage());
        $data = count($data) ? $data : ["exception" => $error];

        logger()->error(  $message, $data);
    }
}
