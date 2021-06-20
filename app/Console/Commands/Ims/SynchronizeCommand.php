<?php

namespace App\Console\Commands\Ims;

use App\Cloud\Cloud;
use App\Models\DeviceData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SynchronizeCommand extends Command
{
    protected $signature = 'ims:synch';

    protected $description = 'Command description';

    public function handle()
    {
        $data = DeviceData::whereIsSynchronized(false)->without(['parameter', 'device']);

        $resp = Cloud::post('cloud/data', ['data' => $data->get()]);

        ray($resp);

        $data->update([
            'is_synchronized' => true,
            'synchronization_time' => Carbon::now(),
        ]);

    }
}
