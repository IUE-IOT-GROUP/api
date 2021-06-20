<?php

namespace App\Http\Controllers;

use App\Models\DeviceData;
use Illuminate\Http\Request;

class CloudController extends Controller
{
    public function __invoke(Request $request)
    {
        foreach ($request->get('data') as $data)
        {
            DeviceData::create($data);
        }
    }
}
