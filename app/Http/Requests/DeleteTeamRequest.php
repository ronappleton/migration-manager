<?php

declare(strict_types=1);

namespace MigrationManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteTeamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:teams,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
