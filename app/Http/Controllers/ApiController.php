<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiModel;

class ApiController extends Controller
{
    //


    public function scrapData(Request $request)
    {
        ApiModel::ScrabSportsResult();

        /*$logapi = new LogApi;
        $logapi->type = $request->type;
        $logapi->name = $request->name;
        $logapi->message = $request->message;
        $logapi->save();*/
   
        return response()->json(["result" => "ok"], 201);
    }
}
