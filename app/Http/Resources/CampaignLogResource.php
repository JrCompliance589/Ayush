<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Format retries with time gap information
        if ($this->relationLoaded('retries') && $this->retries->count() > 0) {
            $previousTime = Carbon::parse($this->created_at);
            $data['retries'] = $this->retries->map(function ($retry, $index) use (&$previousTime) {
                $retryTime = Carbon::parse($retry->created_at);
                $timeGap = $retryTime->diffInSeconds($previousTime);
                $formattedGap = $this->formatTimeGap($timeGap);
                
                $result = [
                    'id' => $retry->id,
                    'attempt' => $index + 1,
                    'status' => $retry->status,
                    'created_at' => $retryTime->format('Y-m-d H:i:s'),
                    'time_gap' => $formattedGap,
                    'time_gap_seconds' => $timeGap,
                    'metadata' => $retry->metadata,
                ];
                
                $previousTime = $retryTime;
                return $result;
            });
        } else {
            $data['retries'] = [];
        }

        return $data;
    }

    /**
     * Format time gap into human readable string
     */
    protected function formatTimeGap(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return $minutes . ' min ' . $remainingSeconds . ' sec';
        } else {
            $hours = floor($seconds / 3600);
            $remainingMinutes = floor(($seconds % 3600) / 60);
            return $hours . ' hr ' . $remainingMinutes . ' min';
        }
    }
}
