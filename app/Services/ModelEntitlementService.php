<?php

namespace App\Services;

use App\Models\ModelProfile;
use App\Models\StorePurchase;
use Carbon\Carbon;

class ModelEntitlementService
{
    /**
     * Get all active purchases for a model.
     */
    public function getActivePurchases(ModelProfile $model)
    {
        return StorePurchase::where('model_id', $model->id)
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->get();
    }

    /**
     * Check if a model has a specific boolean feature.
     */
    public function hasFeature(ModelProfile $model, string $key): bool
    {
        $purchases = $this->getActivePurchases($model);
        
        foreach ($purchases as $purchase) {
            $features = $purchase->feature_snapshot ?? [];
            foreach ($features as $feature) {
                if ($feature['feature_key'] === $key) {
                    if ($feature['value_type'] === 'boolean' && (bool)$feature['feature_value']) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get the value of a feature, resolving multiple purchases.
     */
    public function getFeature(ModelProfile $model, string $key, $default = null)
    {
        $purchases = $this->getActivePurchases($model);
        $values = [];
        $valueType = null;

        foreach ($purchases as $purchase) {
            $features = $purchase->feature_snapshot ?? [];
            foreach ($features as $feature) {
                if ($feature['feature_key'] === $key) {
                    $values[] = $feature['feature_value'];
                    $valueType = $feature['value_type'];
                }
            }
        }

        if (empty($values)) {
            return $default;
        }

        // Resolution logic
        if ($valueType === 'integer' || $valueType === 'decimal') {
            return max($values);
        }

        if ($valueType === 'boolean') {
            return in_array(true, array_map('boolval', $values));
        }

        // For string or json, return the latest purchase value
        return end($values);
    }

    public function getIntegerFeature(ModelProfile $model, string $key, int $default = 0): int
    {
        return (int)$this->getFeature($model, $key, $default);
    }

    public function getBooleanFeature(ModelProfile $model, string $key, bool $default = false): bool
    {
        return (bool)$this->getFeature($model, $key, $default);
    }
}
