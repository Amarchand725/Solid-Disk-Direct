<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name"  => $this->name ?? '',
            "support_email" => $this->support_email ?? '',
            "sale_email" => $this->sale_email ?? '',
            "phone" => $this->phone_number ?? '',
            "currency" => $this->currency_symbol ?? '',
            "country" => $this->country ?? '',
            "address" => $this->address ?? '',
            "day_range" => $this->day_range ?? '',
            "start_time" => $this->start_time 
                    ? Carbon::parse($this->start_time)->format('h:i') 
                    : '',
            "end_time" => $this->end_time 
                    ? Carbon::parse($this->end_time)->format('h:i') 
                    : '',
            "timezone" => $this->timezone ?? '',
            "facebook_link" => $this->facebook_link ?? '',
            "instagram_link" => $this->instagram_link ?? '',
            "linked_in_link" => $this->linked_in_link ?? '',
            "twitter_link" => $this->twitter_link ?? '',
            "logo" => $this->black_logo ? asset(Storage::url($this->black_logo))  : '',
            "favicon" => $this->favicon ? asset(Storage::url($this->favicon))  : '',
        ];        
    }
}
