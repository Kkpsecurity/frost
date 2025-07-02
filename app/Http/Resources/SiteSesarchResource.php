<?php namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class SiteSesarchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    #[ArrayShape(['id' => "mixed", 'match' => "mixed", 'model' => "mixed", 'view_link' => "mixed"])]
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'id'=>$this->id,
            'match'=>$this->match,
            'model'=>$this->model,
            'view_link'=>$this->view_link,
        ];
    }
}
