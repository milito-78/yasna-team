<?php

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Infrastructure\Paginator\SimplePaginator;
use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;

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

if (!function_exists("getImageFullPath")){
    function getImageFullPath(string $image) : string{
        return url("/") . $image;
    }
}


if (!function_exists("simplePaginator")) {
    function simplePaginator(CustomSimplePaginate $paginate):SimplePaginator
    {
        $items = $paginate->items();
        $perPage = $paginate->perPage();
        $currentPage = $paginate->currentPage();
        $hasMore = $paginate->nextPageExist();

        $options = [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page'
        ];
        return Container::getInstance()->makeWith(SimplePaginator::class, compact(
            'items', 'perPage', 'currentPage', 'options','hasMore'
        ));
    }
}

if (!function_exists("customSimplePaginator")){
    function customSimplePaginator(array|\Illuminate\Support\Collection $items,int $per_page,int $current_page,bool $has_more = false): CustomSimplePaginate{
        return new CustomSimplePaginate(
            is_array($items) ? collect($items) : $items,
            $per_page,
            $current_page,
            $has_more ? $current_page + 1 : null
        );
    }
}

if (! function_exists("resourceDateTimeFormat")){
    function resourceDateTimeFormat(Carbon $time = null): string
    {
        return (! is_null($time)) ? $time->toDateTimeString() : Carbon::now();
    }
}
