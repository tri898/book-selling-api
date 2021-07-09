<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaseController extends Controller
{
   
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($message,$result, $code)
    {
      
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, $code);
    }
     /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($message, $errors = [], $code)
    {
        
    	$response = [
            'success' => false,
            'message' => $message,
        ];
        if(!empty($errors)){
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

}
