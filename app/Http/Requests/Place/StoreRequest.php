<?php

namespace App\Http\Requests\Place;

use App\Models\Place;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'parent_id' => ['nullable', 'exists:places,id']
        ];
    }

    public function name()
    {
        return $this->get('name');
    }

    public function parent(): Place|null
    {
        return $this->findParent($this->get('parent_id'));
    }

    private function findParent(?string $id): Place|null
    {
        return Place::find($id);
    }
}
