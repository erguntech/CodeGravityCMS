<?php

namespace App\Services\HumanDesign;

use InvalidArgumentException;

class HumanDesignProfileService
{
    private array $validProfiles = [
        '1-3',
        '1-4',
        '2-4',
        '2-5',
        '3-5',
        '3-6',
        '4-1',
        '4-6',
        '5-1',
        '5-2',
        '6-2',
        '6-3',
    ];

    public function calculate(int $personalitySunLine, int $designSunLine): array
    {
        $this->validateLine($personalitySunLine);
        $this->validateLine($designSunLine);

        $slug = $personalitySunLine . '-' . $designSunLine;

        if (!in_array($slug, $this->validProfiles, true)) {
            throw new InvalidArgumentException("Geçersiz Human Design profili: {$slug}");
        }

        return [
            'slug' => $slug,
            'reason' => "Personality Sun: line {$personalitySunLine}, Design Sun: line {$designSunLine}. Bu nedenle profil {$personalitySunLine}/{$designSunLine} olarak hesaplandı."
        ];
    }

    public function getDisplayName(string $slug): string
    {
        return str_replace('-', '/', $slug) . ' Profil';
    }

    private function validateLine(int $line): void
    {
        if ($line < 1 || $line > 6) {
            throw new InvalidArgumentException("Profil line değeri 1 ile 6 arasında olmalıdır.");
        }
    }
}
