<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{

    //  it is our base controller 
    public function sendResponse($result , $messages){
        return "hello";
        $response = [
            'status' => true,
            'data' => $result,
            'message' => $messages
        ];
        return response()->json($response,200);
    }
    public function errResponse($error , $errorMessage = [], $code = 404){
        $response = [
            'status' => false,
            'message' => $error
        ];
        if(!empty($errorMessage)){
            $response['data'] = $errorMessage;
        }
        return response()->json($response,$code);
    }
}
