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
    public function sendResponse($message, $data = [], $code)
    {
      
    	$response = [
            'success' => true,
            'message' => $message,
        ];
        if(!empty($data)){
            $response['data'] = $data;
        }


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
    /**
     * return new array.
     *
     * @return array
     */
    public function ConvertToArray($object, $number)
    { 
        foreach($object as $item) {
            $newObject[$item['month']] = $item['value'];
        }
        $result = [];
        for($i = 1; $i<= $number; $i++) {
            if( isset($newObject[$i]) ){
                array_push($result,$newObject[$i]);
             }
             else array_push($result,0);
        }
        return $result;
    }

}
