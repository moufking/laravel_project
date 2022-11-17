<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number'=> $this->number,
            'user'=> $this->getUser,
            'lot'=> $this->getLot->libelle,
            'startDate'=>$this->startDate,
            'endDate'=>$this->endDate

        ];
    }
}
