<?php

use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("/gateways/{name}",function (\Illuminate\Http\Request $request,$name){
    echo "<b>Payment Gateway : <i>$name</i></b><br>";
    print_r($request->all());
    echo "<p>waiting to redirect...</p><br>";


    return redirect()->to("/api/v1/gateway-callback/" . $name . "?" . http_build_query(
            array_merge(
                [
                    "success" => true
                ], $request->all()
            )
        ));
});
