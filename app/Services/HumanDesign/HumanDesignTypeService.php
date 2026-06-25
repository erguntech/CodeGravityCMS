<?php

namespace App\Services\HumanDesign;

class HumanDesignTypeService
{
    private array $motorCenters = ['ego', 'solar_plexus', 'sacral', 'root'];

    /**
     * Calculate Human Design Character Type based on defined centers and active channels.
     * 
     * Rules:
     * 1. Sacral Defined -> Generator
     *    - If Motor-to-Throat connection -> Manifesting Generator
     * 2. Sacral NOT Defined + Motor-to-Throat -> Manifestor
     * 3. Sacral NOT Defined + NO Motor-to-Throat + Centers Defined -> Projector
     * 4. No Centers Defined -> Reflector
     * 
     * @param array $definedCenters List of defined center keys
     * @param array $activeChannels List of active channel objects with center_a_slug and center_b_slug
     * @return array
     */
    public function calculate(array $definedCenters, array $activeChannels): array
    {
        $definedCenters = array_map(fn($c) => strtolower(trim($c)), $definedCenters);
        
        if (empty($definedCenters)) {
            return [
                'slug' => 'reflector',
                'reason' => 'Hiçbir tanımlı merkezi olmadığı için Yansıtıcı (Reflector) olarak hesaplandı.'
            ];
        }

        $sacralDefined = in_array('sacral', $definedCenters, true);
        
        // Build connectivity graph for traversal
        $graph = [];
        foreach ($activeChannels as $channel) {
            $cA = strtolower(trim($channel['center_a_slug']));
            $cB = strtolower(trim($channel['center_b_slug']));
            
            if (!isset($graph[$cA])) $graph[$cA] = [];
            if (!isset($graph[$cB])) $graph[$cB] = [];
            
            $graph[$cA][] = $cB;
            $graph[$cB][] = $cA;
        }

        // Check if any motor is connected to Throat (Directly or Indirectly)
        $motorToThroat = false;
        $connectedMotor = null;
        foreach ($this->motorCenters as $motor) {
            if (in_array($motor, $definedCenters, true)) {
                if ($this->canReach($motor, 'throat', $graph)) {
                    $motorToThroat = true;
                    $connectedMotor = $motor;
                    break;
                }
            }
        }

        // 1. Generator / Manifesting Generator
        if ($sacralDefined) {
            if ($motorToThroat) {
                return [
                    'slug' => 'manifesting-generator',
                    'reason' => 'Sakral merkezi tanımlı ve bir motor merkezden Boğaz merkezine aktif kanal bağlantısı var. Bu nedenle Gerçekleştiren Üretici (Manifesting Generator) olarak hesaplandı.'
                ];
            }
            return [
                'slug' => 'generator',
                'reason' => 'Sakral merkezi tanımlı olduğu için Üretici (Generator) olarak hesaplandı.'
            ];
        }

        // 2. Manifestor
        if ($motorToThroat) {
            return [
                'slug' => 'manifestor',
                'reason' => 'Sakral merkezi tanımlı değil, ancak bir motor merkez ('.ucfirst($connectedMotor).') Boğaz merkezine tanımlı kanal ağı üzerinden bağlı. Bu nedenle Gösterici (Manifestor) olarak hesaplandı.'
            ];
        }

        // 3. Projector
        return [
            'slug' => 'projector',
            'reason' => 'Tanımlı merkezleri var ancak Sakral tanımlı değil ve motor merkezlerden Boğaz merkezine bağlantı yok. Bu nedenle Projektör (Projector) olarak hesaplandı.'
        ];
    }

    /**
     * BFS to find if a path exists between two centers in the defined channel graph
     */
    private function canReach(string $start, string $target, array $graph): bool
    {
        if ($start === $target) return true;
        if (!isset($graph[$start])) return false;
        
        $queue = [$start];
        $visited = [$start => true];

        while (!empty($queue)) {
            $current = array_shift($queue);
            
            if ($current === $target) return true;
            
            if (isset($graph[$current])) {
                foreach ($graph[$current] as $neighbor) {
                    if (!isset($visited[$neighbor])) {
                        $visited[$neighbor] = true;
                        $queue[] = $neighbor;
                    }
                }
            }
        }

        return false;
    }
}
