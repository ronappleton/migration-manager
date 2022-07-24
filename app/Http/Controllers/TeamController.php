<?php

declare(strict_types=1);

namespace MigrationManager\Http\Controllers;

use MigrationManager\Http\Requests\CreateTeamRequest;
use MigrationManager\Http\Requests\UpdateTeamRequest;
use MigrationManager\Models\Team;

class TeamController
{
    public function index()
    {
        
    }

    public function create()
    {
        
    }
    
    public function store(CreateTeamRequest $request)
    {
        
    }
    
    public function edit()
    {
        
    }
    
    public function update(UpdateTeamRequest $request, Team $team): Team
    {
        return $team->fill($request->except('_token'));
    }
    
    public function delete(Team $team): ?bool
    {
        return $team->delete();
    }
}
