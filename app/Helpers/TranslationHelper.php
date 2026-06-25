<?php

namespace App\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class TranslationHelper
{
    /**
     * Otomatik çeviri açıksa, varsayılan dildeki veriyi diğer dillere çevirir.
     * 
     * @param array $data
     * @param array $translatableFields Çevirilecek alanlarin listesi, örn: ['title', 'description']
     * @return array
     */
    public static function autoTranslateTranslatableFields(array $data, array $translatableFields): array
    {
        $client = auth()->user()->client;

        // Otomatik çeviri kapalıysa veya client yoksa datayı olduğu gibi döndür
        if (!$client || !$client->auto_translate) {
            return $data;
        }

        $languages = $client->languages()->where('is_active', true)->get();
        if ($languages->isEmpty()) {
            return $data;
        }

        $defaultLang = $languages->where('is_default', true)->first()?->code ?? 'tr';
        $activeLangs = $languages->pluck('code')->toArray();

        // Translator objesini önbelleğe alalım ki her alan/dil için yeni instance oluşturmayalım
        $translator = new GoogleTranslate();

        foreach ($translatableFields as $field) {
            // Eğer data içinde bu alan yoksa veya dizi değilse atla
            if (!isset($data[$field]) || !is_array($data[$field])) {
                continue;
            }

            $defaultText = $data[$field][$defaultLang] ?? null;

            // Varsayılan dilde metin yoksa (veya boşsa) çeviri yapamayız
            if (empty(trim($defaultText ?? ''))) {
                continue;
            }

            foreach ($activeLangs as $langCode) {
                // Sadece boş olan diller için çeviri yap
                if ($langCode !== $defaultLang && empty(trim($data[$field][$langCode] ?? ''))) {
                    try {
                        // Çeviri yap
                        $translator->setSource($defaultLang);
                        $translator->setTarget($langCode);
                        $translatedText = $translator->translate($defaultText);
                        
                        $data[$field][$langCode] = $translatedText;
                    } catch (\Exception $e) {
                        // Log the error but don't break the application
                        Log::error("Otomatik çeviri hatası ({$defaultLang} -> {$langCode}): " . $e->getMessage());
                    }
                }
            }
        }

        return $data;
    }
}
