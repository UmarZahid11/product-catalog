<?php
 
namespace App\Http\Resources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductResource;
 
class CategoryResource extends JsonResource
{
    /**
     * Indicates if the resource's collection keys should be preserved.
     *
     * @var bool
     */
    public $preserveKeys = true;
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(isset($this->id)) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'products' => ProductResource::collection($this->products),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        } return [];
    }
}