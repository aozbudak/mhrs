<?php

namespace App\Http\Controllers\Hospital;

use App\Models\Hospital;

class SaglikMerkeziPanelController extends HospitalPanelController
{
    protected function institutionRouteGroup(): string
    {
        return 'saglik-merkezi';
    }

    protected function authorizedHospital(): Hospital
    {
        $user = $this->authorizedHospitalUser();

        return Hospital::query()
            ->whereKey($user->managed_hospital_id)
            ->where('is_saglik_merkezi', true)
            ->firstOrFail();
    }
}
