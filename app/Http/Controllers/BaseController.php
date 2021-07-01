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
        $status['success'] = true;
        $status['message'] = $message;
    	$response = [
            'status' => $status,
            'data'    => $result
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
        $status['success'] = false;
        $status['message'] = $message;
    	$response = [
            'status' => $status
        ];
        if(!empty($errors)){
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

}
