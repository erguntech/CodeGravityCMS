<?php

namespace App\Traits;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait AutoTranslates
{
    /**
     * After the translation endpoint fails (typically 429 Too Many Requests) we stop
     * calling it for this long, so every save does not pay a network round trip.
     */
    protected static int $autoTranslateBackoffMinutes = 15;

    protected static string $autoTranslateBackoffKey = 'auto_translate_backoff_until';

    /**
     * Boot the auto translates trait for a model.
     *
     * @return void
     */
    public static function bootAutoTranslates()
    {
        static::saving(function ($model) {
            $user = auth()->user();
            if (!$user) {
                return;
            }

            $client = $user->client;
            if (!$client || !$client->auto_translate) {
                return;
            }

            // Check if the model has the translatable attributes property
            if (!method_exists($model, 'getTranslatableAttributes')) {
                return;
            }

            $translatableAttributes = $model->getTranslatableAttributes();
            if (empty($translatableAttributes)) {
                return;
            }

            if (Cache::has(static::$autoTranslateBackoffKey)) {
                return;
            }

            $languages = $client->languages()->where('is_active', true)->get();
            if ($languages->isEmpty()) {
                return;
            }

            $defaultLang = $languages->where('is_default', true)->first()?->code ?? 'tr';
            $activeLangs = $languages->pluck('code')->toArray();

            $translator = new GoogleTranslate();

            foreach ($translatableAttributes as $attribute) {
                if (!$model->isDirty($attribute)) {
                    continue;
                }

                $translations = $model->getTranslations($attribute);
                $defaultText = $translations[$defaultLang] ?? null;

                if (empty(trim($defaultText ?? ''))) {
                    continue;
                }

                // Orjinal verileri al (değişiklik kontrolü için)
                $original = $model->getOriginal($attribute);
                $originalArray = is_string($original) ? json_decode($original, true) : (is_array($original) ? $original : []);

                $oldDefaultText = $originalArray[$defaultLang] ?? null;
                $defaultTextChanged = ($defaultText !== $oldDefaultText);

                $dirty = false;
                foreach ($activeLangs as $langCode) {
                    if ($langCode === $defaultLang) {
                        continue;
                    }

                    $currentLangText = $translations[$langCode] ?? '';
                    $oldLangText = $originalArray[$langCode] ?? '';

                    $langTextChanged = ($currentLangText !== $oldLangText);
                    $isEmpty = empty(trim($currentLangText));

                    // Sadece şu durumlarda çeviri yap:
                    // 1. Hedef dil alanı boşsa
                    // VEYA 2. Ana dil içeriği değişmişse VE kullanıcı o dili o an manuel değiştirmemişse
                    if ($isEmpty || ($defaultTextChanged && !$langTextChanged)) {
                        try {
                            $translator->setSource($defaultLang);
                            $translator->setTarget($langCode);
                            $translatedText = $translator->translate($defaultText);
                            $translations[$langCode] = $translatedText;
                            $dirty = true;
                        } catch (\Exception $e) {
                            Log::error("Otomatik çeviri hatası ({$defaultLang} -> {$langCode}): " . $e->getMessage()
                                . " — otomatik çeviri " . static::$autoTranslateBackoffMinutes . " dakika devre dışı bırakıldı.");
                            Cache::put(static::$autoTranslateBackoffKey, now()->toDateTimeString(), now()->addMinutes(static::$autoTranslateBackoffMinutes));

                            if ($dirty) {
                                $model->setTranslations($attribute, $translations);
                            }

                            return;
                        }
                    }
                }

                if ($dirty) {
                    $model->setTranslations($attribute, $translations);
                }
            }
        });
    }
}
