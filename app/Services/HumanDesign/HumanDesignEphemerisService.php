<?php

namespace App\Services\HumanDesign;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class HumanDesignEphemerisService
{
    protected string $swetestPath;
    protected string $ephePath;
    protected string $nodeType;

    public static array $displayOrder = [
        'sun',
        'earth',
        'north_node',
        'south_node',
        'moon',
        'mercury',
        'venus',
        'mars',
        'jupiter',
        'saturn',
        'uranus',
        'neptune',
        'pluto',
    ];

    public function __construct()
    {
        $this->swetestPath = config('human_design.swetest_path');
        $this->ephePath = config('human_design.ephe_path');
        $this->nodeType = config('human_design.node_type', 'true');
    }

    /**
     * Calculate both Personality and Design positions
     */
    public function calculateFullChart(string $birthDate, string $birthTime, string $timezone, ?float $latitude = null, ?float $longitude = null): array
    {
        // 1. Personality Positions (at Birth)
        $local = Carbon::parse("$birthDate $birthTime", $timezone);
        $birthUtc = $local->copy()->utc();

        $personality = $this->calculatePersonalityPositions($birthDate, $birthTime, $timezone, $latitude, $longitude);
        
        // 2. Find Design Date (Exactly 88 degrees of Sun travel before birth)
        $designUtc = $this->findDesignDate($birthUtc);
        
        // 3. Design Positions (at Design Date)
        $design = $this->calculatePersonalityPositions(
            $designUtc->format('Y-m-d'),
            $designUtc->format('H:i:s.u'),
            'UTC',
            $latitude,
            $longitude
        );

        return [
            'personality' => $personality,
            'design' => $design,
            'design_date_utc' => $designUtc->toDateTimeString(),
            'debug' => [
                'birth_utc' => $birthUtc->toDateTimeString(),
                'design_utc' => $designUtc->toDateTimeString(),
                'personality_sun_longitude' => $personality['positions']['sun'],
                'design_target_sun_longitude' => $this->normalizeDegree($personality['positions']['sun'] - 88),
                'design_sun_longitude' => $design['positions']['sun'],
                'timezone' => $timezone,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]
        ];
    }

    /**
     * Alias for findDesignDate to match CalculationService
     */
    public function calculateDesignDate(Carbon $birthUtc): Carbon
    {
        return $this->findDesignDate($birthUtc);
    }

    /**
     * Calculate positions and return flat array [planet => longitude]
     */
    public function calculatePositions(Carbon $utc): array
    {
        $result = $this->calculatePersonalityPositions(
            $utc->format('Y-m-d'),
            $utc->format('H:i:s.u'),
            'UTC'
        );

        return $result['positions'] ?? [];
    }

    /**
     * Binary search to find the exact moment the Sun was 88 degrees earlier
     */
    public function findDesignDate(Carbon $birthUtc): Carbon
    {
        $personalitySun = $this->calculateSunLongitude($birthUtc);
        $targetSun = $this->normalizeDegree($personalitySun - 88);

        $high = $birthUtc->copy();
        $low = $birthUtc->copy()->subDays(120);

        for ($i = 0; $i < 50; $i++) {
            $midTimestamp = (int) floor(($low->timestamp + $high->timestamp) / 2);
            $mid = Carbon::createFromTimestampUTC($midTimestamp);

            $midSun = $this->calculateSunLongitude($mid);
            $diff = $this->signedDegreeDiff($midSun, $targetSun);

            if (abs($diff) < 0.00001) {
                return $mid;
            }

            if ($diff < 0) {
                $low = $mid;
            } else {
                $high = $mid;
            }
        }

        return Carbon::createFromTimestampUTC((int) floor(($low->timestamp + $high->timestamp) / 2));
    }

    /**
     * Helper to get only Sun longitude
     */
    private function calculateSunLongitude(Carbon $utc): float
    {
        $dateStr = $utc->format('d.m.Y');
        $timeStr = $utc->format('H:i:s');

        $cmd = [
            $this->swetestPath,
            "-edir" . $this->ephePath,
            "-b" . $dateStr,
            "-ut" . $timeStr,
            "-p0",
            "-fPl",
            "-g,"
        ];

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception('Sun longitude hesaplanamadı.');
        }

        $output = $process->getOutput();

        if (preg_match('/Sun\s*,\s*([0-9]+(?:\.[0-9]+)?)/i', $output, $matches)) {
            return $this->normalizeDegree((float) $matches[1]);
        }

        throw new \Exception('Sun longitude parse edilemedi. Output: ' . $output);
    }

    /**
     * Calculate planetary positions for a given time
     */
    public function calculatePersonalityPositions(string $birthDate, string $birthTime, string $timezone, ?float $latitude = null, ?float $longitude = null): array
    {
        try {
            $local = Carbon::parse("$birthDate $birthTime", $timezone);
        } catch (\Exception $e) {
            $local = Carbon::createFromFormat('Y-m-d H:i:s', "$birthDate $birthTime", $timezone);
        }
        $utc = $local->copy()->utc();

        $positions = [];
        $rawByPlanet = [];
        $commands = [];

        // Call 1: Planets 0-9
        try {
            $cmd = $this->getSwetestCommand($utc, "0123456789");
            $commands[] = implode(' ', $cmd);
            $rawOutput = $this->runSwetest($cmd);
            $lines = preg_split('/\r\n|\r|\n/', trim($rawOutput));
            
            foreach ($lines as $line) {
                $parts = array_map('trim', explode(',', $line));
                if (count($parts) < 2) continue;

                $planetName = strtolower($parts[0]);
                $lon = (float) $parts[1];
                $key = match($planetName) {
                    'sun' => 'sun',
                    'moon' => 'moon',
                    'mercury' => 'mercury',
                    'venus' => 'venus',
                    'mars' => 'mars',
                    'jupiter' => 'jupiter',
                    'saturn' => 'saturn',
                    'uranus' => 'uranus',
                    'neptune' => 'neptune',
                    'pluto' => 'pluto',
                    default => null
                };

                if ($key) {
                    $positions[$key] = $this->normalizeDegree($lon);
                    $rawByPlanet[$key] = $line;
                }
            }
        } catch (\Exception $e) {
            Log::error("Planets call failed: " . $e->getMessage());
        }

        // Call 2: Node
        try {
            $nodePlanet = ($this->nodeType === 'true' ? 't' : 'm');
            $cmd = $this->getSwetestCommand($utc, $nodePlanet);
            $commands[] = implode(' ', $cmd);
            $rawNodeOutput = $this->runSwetest($cmd);
            
            if (preg_match('/(?:mean Node|true Node)\s*,\s*([0-9]+(?:\.[0-9]+)?)/i', $rawNodeOutput, $matches)) {
                $nodeLon = (float) $matches[1];
                $positions['north_node'] = $this->normalizeDegree($nodeLon);
                $rawByPlanet['north_node'] = trim($rawNodeOutput);
                
                $positions['south_node'] = $this->normalizeDegree($nodeLon + 180);
                $rawByPlanet['south_node'] = "Calculated from North Node + 180";
            }
        } catch (\Exception $e) {
            Log::error("Node call failed: " . $e->getMessage());
        }

        // Earth
        if (isset($positions['sun'])) {
            $positions['earth'] = $this->normalizeDegree($positions['sun'] + 180);
            $rawByPlanet['earth'] = "Calculated from Sun + 180";
        }

        return [
            'datetime_utc' => $utc->toDateTimeString(),
            'positions' => $positions,
            'raw' => $rawByPlanet,
            'commands' => $commands,
        ];
    }

    private function getSwetestCommand(Carbon $utc, string $planetParam): array
    {
        return [
            $this->swetestPath,
            "-edir" . $this->ephePath,
            "-b" . $utc->format('d.m.Y'),
            "-ut" . $utc->format('H:i:s.u'),
            "-p" . $planetParam,
            "-fPl",
            "-g,"
        ];
    }

    private function runSwetest(array $command): string
    {
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) throw new ProcessFailedException($process);
        return $process->getOutput();
    }

    private function signedDegreeDiff(float $current, float $target): float
    {
        $diff = $this->normalizeDegree($current - $target);
        if ($diff > 180) $diff -= 360;
        return $diff;
    }

    private function normalizeDegree(float $degree): float
    {
        $degree = fmod($degree, 360.0);
        if ($degree < 0) $degree += 360.0;
        return round($degree, 6);
    }
}
