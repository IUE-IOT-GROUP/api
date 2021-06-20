<?php

namespace App\Http\Controllers;

use App\Models\DeviceData;

class CloudController extends Controller
{
    public function __invoke(\Request $request)
    {
        foreach ($request->get('data') as $data)
        {
            DeviceData::create($data);
        }
    }
}
