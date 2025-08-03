<?php

namespace App\Http\Resources;

use App\Http\Resources\SlotResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id'   => $this->id,
			'user' => [
				'id'   => $this->user->id,
				'name' => $this->user->name,
			],
			'slots'      => SlotResource::collection($this->whenLoaded('slots')),
			'created_at' => $this->created_at,
		];
	}
}
