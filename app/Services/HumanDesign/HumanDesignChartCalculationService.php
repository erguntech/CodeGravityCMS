<?php

namespace App\Services\HumanDesign;

use App\Models\HumanDesign\HdChart;
use App\Models\HumanDesign\HdGate;
use App\Models\HumanDesign\HdChannel;
use App\Models\HumanDesign\HdProfile;
use App\Models\HumanDesign\HdAuthority;
use App\Models\HumanDesign\HdType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HumanDesignChartCalculationService
{
    private array $planetOrder = [
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

    public function __construct(
        private HumanDesignEphemerisService $ephemerisService,
        private HumanDesignGateService $gateService,
        private HumanDesignChannelService $channelService,
        private HumanDesignTypeService $typeService,
        private HumanDesignAuthorityService $authorityService,
        private HumanDesignProfileService $profileService
    ) {}

    /**
     * Calculate and store Human Design Chart
     * 
     * @param array $input [user_id, birth_date, birth_time, timezone, latitude, longitude]
     * @return array
     */
    public function calculate(array $input): array
    {
        $gender = $input['gender'] ?? 'Erkek';

        // 1. Create birth datetime local and convert to UTC
        $birthLocal = Carbon::parse($input['birth_date'] . ' ' . $input['birth_time'], $input['timezone']);
        $birthUtc = $birthLocal->copy()->timezone('UTC');

        // 2. Personality positions (at birth UTC)
        $personalityPositions = $this->ephemerisService->calculatePositions($birthUtc);

        // 3. Design date (88 degrees of Solar longitude before birth)
        $designDateUtc = $this->ephemerisService->calculateDesignDate($birthUtc);

        // 4. Design positions (at design date UTC)
        $designPositions = $this->ephemerisService->calculatePositions($designDateUtc);

        // 5. Personality activations
        $personalityActivations = $this->buildActivations($personalityPositions);

        // 6. Design activations
        $designActivations = $this->buildActivations($designPositions);

        // 7. Combine all gates and track sources
        $gateSources = [];
        foreach ($personalityActivations as $planet => $act) {
            $gateNum = (int)$act['gate'];
            if (!isset($gateSources[$gateNum])) $gateSources[$gateNum] = [];
            $gateSources[$gateNum][] = [
                'source' => 'Personality',
                'planet' => $planet,
                'line' => $act['line'],
                'gate_line' => $act['gate_line']
            ];
        }
        foreach ($designActivations as $planet => $act) {
            $gateNum = (int)$act['gate'];
            if (!isset($gateSources[$gateNum])) $gateSources[$gateNum] = [];
            $gateSources[$gateNum][] = [
                'source' => 'Design',
                'planet' => $planet,
                'line' => $act['line'],
                'gate_line' => $act['gate_line']
            ];
        }

        $activeGates = array_keys($gateSources);
        sort($activeGates);

        // 8. Active channels
        $channelsData = $this->channelService->calculate($activeGates);
        
        // 9. Defined centers
        $definedCenterSlugs = collect($channelsData)
            ->flatMap(fn($channel) => [
                $channel['center_a_slug'],
                $channel['center_b_slug'],
            ])
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // 10. Type calculation
        $typeResult = $this->typeService->calculate($definedCenterSlugs, $channelsData);
        $type = HdType::where('slug', $typeResult['slug'])->first();

        // 11. Authority calculation
        $authorityResult = $this->authorityService->calculate($definedCenterSlugs, $channelsData);
        $authority = HdAuthority::where('slug', $authorityResult['slug'])->first();

        // 12. Profile calculation
        $profileResult = $this->profileService->calculate($personalityActivations['sun']['line'], $designActivations['sun']['line']);
        $profile = HdProfile::where('slug', $profileResult['slug'])->first();

        $reasons = [
            'type' => $typeResult['reason'],
            'authority' => $authorityResult['reason'],
            'profile' => $profileResult['reason'],
        ];

        // 13. Create or update chart
        $chart = HdChart::updateOrCreate(
            ['user_id' => $input['user_id'] ?? null], 
            [
                'gender' => $gender,
                'birth_date' => $input['birth_date'],
                'birth_time' => $input['birth_time'],
                'birth_timezone' => $input['timezone'],
                'birth_latitude' => $input['latitude'],
                'birth_longitude' => $input['longitude'],

                'personality_positions' => $personalityPositions,
                'design_positions' => $designPositions,

                'personality_activations' => $personalityActivations,
                'design_activations' => $designActivations,

                'active_gates' => $activeGates,
                'active_channels' => $channelsData,
                'defined_centers' => $definedCenterSlugs,

                'character_type_id' => $type?->id,
                'authority_id' => $authority?->id,
                'profile_id' => $profile?->id,

                'calculation_debug' => [
                    'birth_local' => $birthLocal->toDateTimeString(),
                    'birth_utc' => $birthUtc->toDateTimeString(),
                    'design_utc' => $designDateUtc->toDateTimeString(),
                    'timezone' => $input['timezone'],
                    'latitude' => $input['latitude'],
                    'longitude' => $input['longitude'],
                ],
                'reasons' => $reasons,
                'calculated_at' => now(),
            ]
        );

        // 14. Fetch detailed data for results
        $allCenters = \App\Models\HumanDesign\HdEnergyCenter::orderBy('sort_order')->get()->map(function($center) use ($definedCenterSlugs, $channelsData) {
            $isDefined = in_array($center->slug, $definedCenterSlugs);
            
            // Find channels connected to this center
            $connectedChannels = collect($channelsData)->filter(function($ch) use ($center) {
                return $ch['center_a_slug'] === $center->slug || $ch['center_b_slug'] === $center->slug;
            })->values()->toArray();

            return [
                'name' => $center->name,
                'turkish_name' => $center->turkish_name,
                'slug' => $center->slug,
                'is_defined' => $isDefined,
                'status_text' => $isDefined ? 'Tanımlı' : 'Tanımsız',
                'color_status' => $isDefined ? 'Renkli' : 'Beyaz',
                'color' => $center->color,
                'description' => $isDefined 
                    ? ($center->defined_description_tr ?: $center->defined_description ?: $center->description_tr ?: $center->description)
                    : ($center->undefined_description_tr ?: $center->undefined_description ?: $center->description_tr ?: $center->description),
                'general_description' => $center->general_description_tr ?: $center->general_description,
                'channels' => $connectedChannels
            ];
        });

        $detailedGates = \App\Models\HumanDesign\HdGate::whereIn('number', $activeGates)->get()->map(function($gate) use ($gateSources) {
            return [
                'number' => $gate->number,
                'name' => $gate->turkish_name ?: $gate->name,
                'description' => $gate->description_tr ?: $gate->description ?: 'Bu geçit için açıklama henüz girilmemiş.',
                'what_to_do' => $gate->what_to_do_tr,
                'activations' => $gateSources[$gate->number] ?? [],
                'type' => collect($gateSources[$gate->number] ?? [])->pluck('source')->unique()->implode(' + ')
            ];
        })->sortBy('number')->values();

        $detailedChannels = collect($channelsData)->map(function($ch) {
            $model = HdChannel::where('code', $ch['code'])->first();
            return [
                'code' => $ch['code'],
                'name' => $model?->turkish_name ?: $ch['name'],
                'description' => $model?->description_tr ?: $model?->description ?: 'Bu kanal için açıklama henüz girilmemiş.',
                'what_to_do' => $model?->what_to_do_tr,
                'gate_a' => $ch['gate_a'],
                'gate_b' => $ch['gate_b'],
                'center_a' => $ch['center_a_slug'],
                'center_b' => $ch['center_b_slug'],
                'result_text' => "Bu kanal " . ucfirst($ch['center_a_slug']) . " ve " . ucfirst($ch['center_b_slug']) . " merkezlerini tanımlı hale getirdi."
            ];
        });

        return [
            'chart_id' => $chart->id,
            'gender' => $gender,
            'personality_activations' => $personalityActivations,
            'design_activations' => $designActivations,
            'active_gates' => $activeGates,
            'active_channels' => $channelsData,
            'defined_centers' => $definedCenterSlugs,
            'reasons' => $reasons,
            'all_centers' => $allCenters,
            'detailed_gates' => $detailedGates,
            'detailed_channels' => $detailedChannels,
            'type' => $type ? array_merge($type->toArray(), [
                'image_url' => $gender == 'Kadın' 
                    ? ($type->image_female ? asset('storage/' . $type->image_female) : null)
                    : ($type->image_male ? asset('storage/' . $type->image_male) : null),
                'description_tr' => $type->description_tr ?: $type->description,
                'strategy_tr' => $type->strategy_tr,
                'signature_tr' => $type->signature_tr,
                'not_self_theme_tr' => $type->not_self_theme_tr,
            ]) : null,
            'authority' => $authority ? array_merge($authority->toArray(), [
                'description_tr' => $authority->description_tr ?: $authority->description,
            ]) : null,
            'profile' => $profile ? array_merge($profile->toArray(), [
                'description_tr' => $profile->description_tr ?: $profile->description,
                'display_name' => $this->profileService->getDisplayName($profile->slug)
            ]) : null,
        ];
    }

    private function buildActivations(array $positions): array
    {
        $activations = [];

        foreach ($this->planetOrder as $planet) {
            if (!isset($positions[$planet])) {
                continue;
            }

            $gateData = $this->gateService->calculate((float) $positions[$planet]);

            $activations[$planet] = [
                'planet' => $planet,
                'longitude' => $gateData['longitude'],
                'gate' => $gateData['gate'],
                'line' => $gateData['line'],
                'gate_line' => $gateData['gate_line'],
                'hd_degree' => $gateData['hd_degree'] ?? null,
                'degree_inside_gate' => $gateData['degree_inside_gate'] ?? null,
            ];
        }

        return $activations;
    }
}
