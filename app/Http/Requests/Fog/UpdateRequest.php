<?php

namespace App\Http\Requests\Fog;

use App\Models\Place;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'place_id' => ['bail', 'required', Rule::exists('places', 'id'), function ($attribute, $value, $fail) {
                $exists = Place::where('id', $this->place_id)->where('user_id', $this->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given place cannot be found');
                }
            }],
            'mac_address' => ['required'],
            'ip_address' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'fog_id.exists' => 'The given fog does not exist.',
        ];
    }

    public function name()
    {
        return $this->get('name');
    }

    public function macAddress()
    {
        return $this->get('mac_address');
    }

    public function ipAddress()
    {
        return $this->get('ip_address');
    }

    public function port()
    {
        return $this->get('port');
    }

    public function place(): ?Place
    {
        return $this->findPlace($this->get('parent_id'));
    }

    private function findPlace(string $id): ?Place
    {
        return Place::find($id);
    }
}
