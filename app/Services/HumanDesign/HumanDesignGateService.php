<?php

namespace App\Services\HumanDesign;

class HumanDesignGateService
{
    /**
     * Human Design Gate Order (Rave Mandala)
     * Starts from Gate 41 (0° Aquarius / 300° Tropical)
     */
    private array $gateOrder = [
        41, 19, 13, 49, 30, 55, 37, 63,
        22, 36, 25, 17, 21, 51, 42, 3,
        27, 24, 2, 23, 8, 20, 16, 35,
        45, 12, 15, 52, 39, 53, 62, 56,
        31, 33, 7, 4, 29, 59, 40, 64,
        47, 6, 46, 18, 48, 57, 32, 50,
        28, 44, 1, 43, 14, 34, 9, 5,
        26, 11, 10, 58, 38, 54, 61, 60,
    ];

    /**
     * Calculate Gate and Line from longitude
     */
    public function calculate(float $longitude): array
    {
        // 1. Normalize degree relative to Gate 41
        // Using +58 offset as requested for Human Design alignment
        $hdDegree = $this->normalizeDegree($longitude + 58);

        $gateSize = 360 / 64; // 5.625
        $lineSize = $gateSize / 6; // 0.9375

        // 2. Find Gate Index
        $gateIndex = (int) floor($hdDegree / $gateSize);

        // Bound check
        if ($gateIndex > 63) {
            $gateIndex = 63;
        }

        $gate = $this->gateOrder[$gateIndex];

        // 3. Find Degree inside the Gate
        $degreeInsideGate = $hdDegree - ($gateIndex * $gateSize);

        // 4. Find Line (1 to 6)
        $line = (int) floor($degreeInsideGate / $lineSize) + 1;

        // Bound check
        if ($line > 6) {
            $line = 6;
        }

        return [
            'longitude' => round($longitude, 6),
            'hd_degree' => round($hdDegree, 6),
            'gate' => $gate,
            'line' => $line,
            'gate_line' => $gate . '.' . $line,
            'gate_index' => $gateIndex,
            'degree_inside_gate' => round($degreeInsideGate, 6),
        ];
    }

    /**
     * Bulk calculate for multiple planets
     */
    public function calculateAll(array $positions): array
    {
        $activations = [];
        foreach ($positions as $planet => $longitude) {
            if ($longitude !== null) {
                $activations[$planet] = $this->calculate($longitude);
            } else {
                $activations[$planet] = null;
            }
        }
        return $activations;
    }

    /**
     * Normalize degree to 0-360
     */
    private function normalizeDegree(float $degree): float
    {
        $degree = fmod($degree, 360.0);

        if ($degree < 0) {
            $degree += 360.0;
        }

        return $degree;
    }
}
