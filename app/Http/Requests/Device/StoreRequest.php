<?php

namespace App\Http\Requests\Device;

use App\Models\DeviceType;
use App\Models\Fog;
use App\Models\Place;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['nullable'],
            'name' => ['required'],
            'fog_id' => ['bail', 'required', Rule::exists('fogs', 'id'), function ($attribute, $value, $fail) {
                $exists = Fog::where('id', $this->fog_id)->where('user_id', $this->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given fog cannot be found');
                }
            }],
            'place_id' => ['bail', 'required', Rule::exists('places', 'id'), function ($attribute, $value, $fail) {
                $exists = Place::where('id', $this->place_id)->where('user_id', $this->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given place cannot be found');
                }
            }],
            'device_type_id' => ['nullable', Rule::unique('devices')->where(function (Builder $query) {
                return $query->where([
                    ['mac_address', '=', $this->mac_address],
                    ['ip_address', '=', $this->ip_address],
                    ['device_type_id', '=', $this->device_type_id],
                    ['fog_id', '=', $this->fog_id],
                ]);
            })],
            'mac_address' => ['required'],
            'ip_address' => ['required'],
            'parameters' => ['required', 'array'],
        ];
    }

    public function messages()
    {
        return [
            'fog_id.exists' => 'The given place does not exist.',
            'device_id.unique' => 'The given device is already available within the same place',
        ];
    }

    public function id()
    {
        return $this->get('id');
    }

    public function macAddress()
    {
        return $this->get('mac_address');
    }

    public function ipAddress()
    {
        return $this->get('ip_address');
    }

    public function parameters()
    {
        return $this->get('parameters');
    }

    public function fog(): ?Fog
    {
        return $this->findFog($this->get('fog_id'));
    }

    private function findFog(?string $id): ?Fog
    {
        return Fog::find($id);
    }

    public function place(): ?Place
    {
        return $this->findPlace($this->get('place_id'));
    }

    private function findPlace(?string $id): ?Place
    {
        return Place::find($id);
    }

    public function deviceType(): ?DeviceType
    {
        return $this->findDeviceType($this->get('device_type_id'));
    }

    private function findDeviceType(?string $id): ?DeviceType
    {
        return $id
            ? DeviceType::find($id)
            : DeviceType::firstOrCreate(['name' => $this->name()]);
    }

    public function name()
    {
        return $this->get('name');
    }
}
