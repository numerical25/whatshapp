<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class JsonApiErrorResource extends Resource
{
    static $wrap = "errors";


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $info = "";
        $data =  [
                [
                    'id'            => "500",
                    'detail' => $this->getMessage(),
                ]
            ];
        return $data;
    }
}
