<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\ModelProfile;

class AgencyModelLimitSyncService
{
    protected $featureLimitService;

    public function __construct(FeatureLimitService $featureLimitService)
    {
        $this->featureLimitService = $featureLimitService;
    }

    /**
     * Synchronize model limits for an agency.
     */
    public function sync(Agency $agency): void
    {
        $maxModels = $this->featureLimitService->getMaxAgencyModels($agency);

        $models = ModelProfile::where('agency_id', $agency->id)
            ->where('model_type', 'agency')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($models as $index => $model) {
            $isExceeded = $index >= $maxModels;

            $updateData = ['is_limit_exceeded' => $isExceeded];
            if ($isExceeded) {
                if (is_null($model->limit_exceeded_at)) {
                    $updateData['limit_exceeded_at'] = now();
                }
                if ($model->status === 'active') {
                    $updateData['status'] = 'passive';
                }
            } else {
                $updateData['limit_exceeded_at'] = null;
            }

            $model->update($updateData);
        }
    }
}
