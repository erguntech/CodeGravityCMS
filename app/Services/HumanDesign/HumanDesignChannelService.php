<?php

namespace App\Services\HumanDesign;

use App\Models\HumanDesign\HdChannel;

class HumanDesignChannelService
{
    /**
     * Calculate active channels based on active gates.
     * 
     * Rule: If both gates of a channel are active, the channel is active.
     * Checks all 36 channels in the database.
     * 
     * @param array $activeGates
     * @return array
     */
    public function calculate(array $activeGates): array
    {
        $activeGates = array_map('intval', $activeGates);

        // Fetch all 36 channels
        $allChannels = HdChannel::where('is_active', true)->get();

        $activeChannels = $allChannels->filter(function ($channel) use ($activeGates) {
            $gateA = (int) $channel->gate_a;
            $gateB = (int) $channel->gate_b;

            // Rule: Both gates must be in the active gates list
            return in_array($gateA, $activeGates, true) && in_array($gateB, $activeGates, true);
        });

        return $activeChannels->values()->toArray();
    }
}
