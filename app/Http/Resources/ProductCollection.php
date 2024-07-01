<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * @var array
     * 
     */
    private $meta;

    /**
     * @var array
     * 
     */
    private $links;

    public function __construct($resource)
    {
        if ($resource) {
            $this->links = [
                'first' => $resource->path() . '?page=1',
                'last' => $resource->path() . '?page=' . $resource->total(),
            ];

            $this->meta = [
                'total' => $resource->total(),
                'count' => $resource->count(),
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'total_pages' => $resource->lastPage(),
                'path' => $resource->path()
            ];

            $resource = $resource->getCollection();
            parent::__construct($resource);
        } else {
            $this->links = [];
            $this->meta = [];
        }
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'links' => $this->links,
            'meta' => $this->meta
        ];
    }
}
