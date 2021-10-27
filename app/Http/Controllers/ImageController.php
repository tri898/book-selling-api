<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Http\Controllers\BaseController as BaseController;

class ImageController extends BaseController
{
    public function store(ImageRequest $request) {
        $fields = $request->validated();

        if($fields['type'] ==1) $folder = 'users';
        if($fields['type'] ==2) $folder = 'authors';
        if($fields['type'] ==3) $folder = 'books';
        if($fields['type'] ==4) $folder = 'sliders';

        
        $getName = date('YmdHis') . '.' . $fields['image']->getClientOriginalExtension();
        if(file_exists($getName)) {
            $getName = date('YmdHis') . 'f'. '.' . $fields['image']->getClientOriginalExtension();
        }
        // move image file to public images/$folder
        $fields['image']->move(public_path('images/' .$folder), $getName);
        $link = $folder .'/'.  $getName;
        return $this->sendResponse('Đã lưu ảnh thành công.',$link,200);
    }
}
