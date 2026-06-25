<?php

namespace App\Services\HumanDesign;

class HumanDesignAuthorityService
{
    /**
     * Calculate the Human Design Authority
     *
     * Priority:
     * 1. Solar Plexus → Emotional Authority
     * 2. Sacral → Sacral Authority
     * 3. Spleen → Splenic Authority
     * 4. Ego → Ego Authority
     * 5. G Center → Self Projected
     * 6. None → Mental / Outer Authority
     */
    public function calculate(array $definedCenters, array $activeChannels): array
    {
        // Normalize inputs
        $definedCenters = array_map(fn($center) => strtolower(trim($center)), $definedCenters);

        // 1. Solar Plexus
        if (in_array('solar_plexus', $definedCenters, true)) {
            return [
                'slug' => 'emotional',
                'reason' => 'Solar Plexus merkezi tanımlı. Otorite öncelik sıralamasında Solar Plexus en üst sıradadır. Bu nedenle otorite Duygular Otoritesi olarak hesaplandı.'
            ];
        }

        // 2. Sacral
        if (in_array('sacral', $definedCenters, true)) {
            return [
                'slug' => 'sacral',
                'reason' => 'Solar Plexus tanımlı değil ancak Sacral merkezi tanımlı. Bu nedenle otorite Sakral Otorite olarak hesaplandı.'
            ];
        }

        // 3. Spleen
        if (in_array('spleen', $definedCenters, true)) {
            return [
                'slug' => 'splenic',
                'reason' => 'Solar Plexus ve Sacral tanımlı değil ancak Dalak (Spleen) merkezi tanımlı. Bu nedenle otorite Dalak Otoritesi olarak hesaplandı.'
            ];
        }

        // 4. Ego
        if (in_array('ego', $definedCenters, true)) {
            return [
                'slug' => 'ego',
                'reason' => 'Solar Plexus, Sacral ve Dalak tanımlı değil ancak Ego merkezi tanımlı. Bu nedenle otorite Ego Otoritesi olarak hesaplandı.'
            ];
        }

        // 5. G Center
        if (in_array('g', $definedCenters, true)) {
            return [
                'slug' => 'self-projected',
                'reason' => 'Solar Plexus, Sacral, Dalak ve Ego tanımlı değil ancak G merkezi tanımlı. Bu nedenle otorite Self Projected Otorite olarak hesaplandı.'
            ];
        }

        // 6. Outer Authority
        return [
            'slug' => 'outer',
            'reason' => 'Hiçbir otorite merkezi tanımlı değil. Bu nedenle otorite Zihinsel / Dış Otorite (Mental / Outer Authority) olarak hesaplandı.'
        ];
    }
}
