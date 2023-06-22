<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogApi;

class LogApiController extends Controller
{
    //


    public function store(Request $request)
    {
        /*$logapi = new LogApi;
        $logapi->type = $request->type;
        $logapi->name = $request->name;
        $logapi->message = $request->message;
        $logapi->save();*/
 
        return response()->json(["result" => "ok"], 201);
    }
}
