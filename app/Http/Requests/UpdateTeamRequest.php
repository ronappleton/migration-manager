<?php

declare(strict_types=1);

namespace MigrationManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'locale' => 'sometimes|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
