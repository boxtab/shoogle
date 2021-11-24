<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WellbeingWeekResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'wellbeing' => [
                'social'        => $this->resource['wellbeing']['social'],
                'physical'      => $this->resource['wellbeing']['physical'],
                'mental'        => $this->resource['wellbeing']['mental'],
                'financial'     => $this->resource['wellbeing']['economical'],
                'spiritual'     => $this->resource['wellbeing']['spiritual'],
                'emotional'     => $this->resource['wellbeing']['emotional'],
                'intellectual'  => $this->resource['wellbeing']['intellectual'],
            ],
            'wellbeingData' => [
                'social'        => $this->resource['wellbeingData']['social'],
                'physical'      => $this->resource['wellbeingData']['physical'],
                'mental'        => $this->resource['wellbeingData']['mental'],
                'financial'     => $this->resource['wellbeingData']['economical'],
                'spiritual'     => $this->resource['wellbeingData']['spiritual'],
                'emotional'     => $this->resource['wellbeingData']['emotional'],
                'intellectual'  => $this->resource['wellbeingData']['intellectual'],
                'label'         => $this->resource['wellbeingData']['label'],
            ],
        ];
    }
}
