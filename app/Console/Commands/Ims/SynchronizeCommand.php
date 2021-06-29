<?php

namespace App\Console\Commands\Ims;

use App\Cloud\Cloud;
use App\Models\DeviceData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SynchronizeCommand extends Command
{
    protected $signature = 'ims:sync';

    protected $description = 'Command description';

    public function handle()
    {
        $data = DeviceData::whereIsSynchronized(false)->without(['parameter', 'device']);

        $data->chunk(500, function ($deviceData) {
            $resp = Cloud::post('cloud/data', ['data' => $deviceData]);

            $deviceData->each->update([
                'is_synchronized' => true,
                'synchronization_time' => Carbon::now(),
            ]);
        });
    }
}
