<?php

namespace App\Console\Commands\Ims;

use App\Cloud\Cloud;
use App\Models\DeviceData;
use Illuminate\Console\Command;

class SynchronizeCommand extends Command
{
    protected $signature = 'ims:synch';

    protected $description = 'Command description';

    public function handle()
    {
        $data = DeviceData::whereIsSynchronized(false)->get();

        $resp = Cloud::post('cloud/data', ['data' => $data]);

        ray($resp);

        $data->each->markAsSynchronized();

    }
}
