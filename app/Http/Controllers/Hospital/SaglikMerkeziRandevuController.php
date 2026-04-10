<?php

namespace App\Http\Controllers\Hospital;

class SaglikMerkeziRandevuController extends HospitalRandevuController
{
    protected function institutionRouteGroup(): string
    {
        return 'saglik-merkezi';
    }
}
