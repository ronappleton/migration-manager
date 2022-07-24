<?php

declare(strict_types=1);

namespace MigrationManager\Http\Controllers\Api\User;

use MigrationManager\Http\Controllers\ApiBaseController;
use MigrationManager\Http\Requests\Register as RegisterRequest;
use MigrationManager\Models\User;

class Register extends ApiBaseController
{
    public function register(RegisterRequest $request): void
    {
        $data = $request->validated();
        
        $user = User::query()->create($data);
        
        $user->roles()->attach('personal');
    }
    
    public function registerBusiness(RegisterRequest $request): void
    {
        $data = $request->validated();
        
        $user = User::query()->create($data);
        
        $user->roles()->attach(['personal', 'business']);
    }
}
