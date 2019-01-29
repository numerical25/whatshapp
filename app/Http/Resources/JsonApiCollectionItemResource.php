<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class JsonApiCollectionItemResource extends Resource
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
        $data =  [
            'type'          => $type,
            'id'            => (string)$this->id,
            'attributes'    => $this->resource->attributesToArray(),
            'relationships' => [],
            'links'         => []
        ];
        foreach($relations as $key=>$item) {
            $child = $this->getAttribute($key);
            if($child instanceof \Illuminate\Database\Eloquent\Collection) {
                $data['relationships'][$key] = [];
                foreach($child as $c) {
                    $data['relationships'][$key][] = new JsonApiRelationshipResource($c);
                }
            } else {
                $data['relationships'][$key] = new JsonApiRelationshipResource($child);
            }
        }
        return $data;
    }
}
