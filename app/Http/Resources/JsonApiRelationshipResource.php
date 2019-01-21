<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class JsonApiRelationshipResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = strtolower(class_basename($this->resource));
        $relations = $this->relationsToArray();
        $relate = $this->relations;
        $data =  [
            'links' => [],
            'data' => [
                'type'          => $type,
                'id'            => (string)$this->id,
                'attributes'    => $this->resource->attributesToArray()
            ]
        ];
        
        return $data;
    }
}
