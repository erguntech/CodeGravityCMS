<?php

namespace App\Traits;

trait ReturnsApiTranslations
{
    public function toArray()
    {
        $attributes = parent::toArray();
        
        if (request()->is('api/*') && method_exists($this, 'getTranslatableAttributes')) {
            $locale = app()->getLocale();
            
            foreach ($this->getTranslatableAttributes() as $field) {
                $translation = $this->getTranslation($field, $locale, false);
                
                if (empty($translation)) {
                    $translations = $this->getTranslations($field);
                    $translation = !empty($translations) ? reset($translations) : null;
                }
                
                $attributes[$field] = $translation;
            }
        }
        
        return $attributes;
    }
}
