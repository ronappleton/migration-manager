<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @throws Exception
     * @phpcs:disable SlevomatCodingStandard.Files.FunctionLength.FunctionLength
     * @phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (!$tableNames || !count($tableNames)) {
            throw new Exception(
                'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.',
            );
        }
        
        if ($teams && ($columnNames['team_foreign_key'] ?? '') === '') {
            $exception = 'Error: team_foreign_key on config/permission.php not loaded. ';
            $exception .= 'Run [php artisan config:clear] and try again.';
            
            throw new Exception($exception);
        }

        $this->createPermissionsTable($tableNames['permissions']);
        $this->createRolesTable($tableNames['roles'], $teams, $columnNames);
        $this->createModelHasPermissionsTable($tableNames['model_has_permissions'], $tableNames, $columnNames, $teams);
        $this->createModelHasRolesTable($tableNames['model_has_roles'], $tableNames, $columnNames, $teams);
        $this->createRoleHasPermissionsTable($tableNames['role_has_permissions'], $tableNames);

        app('cache')
            ->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     * 
     * @throws Exception
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (!$tableNames || !count($tableNames)) {
            $exception = 'Error: config/permission.php not found and defaults could not be merged. ';
            $exception .= 'Please publish the package configuration before proceeding, or drop the tables manually.';

            throw new Exception($exception);
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }

    private function createPermissionsTable(string $name): void
    {
        Schema::create($name, static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name', 125);
            $table->string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });
    }

    /**
     * @param array<string, string> $columnNames
     */
    private function createRolesTable(string $name, bool $teams, array $columnNames): void
    {
        Schema::create($name, static function (Blueprint $table) use ($teams, $columnNames): void {
            $table->bigIncrements('id');
            
            // permission.testing is a fix for sqlite testing
            if ($teams || config('permission.testing')) { 
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            
            $table->string('name', 125);
            $table->string('guard_name', 125); 
            $table->timestamps();
            
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });
    }

    /**
     * @param array<string, string> $tableNames
     * @param array<string, string> $columnNames
     */
    private function createModelHasPermissionsTable(
        string $name,
        array $tableNames,
        array $columnNames,
        bool $teams,
    ): void {
        Schema::create($name, static function (Blueprint $table) use ($tableNames, $columnNames, $teams): void {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'],
                          'model_has_permissions_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [
                        $columnNames['team_foreign_key'],
                        PermissionRegistrar::$pivotPermission,
                        $columnNames['model_morph_key'],
                        'model_type',
                    ],
                    'model_has_permissions_permission_model_type_primary',
                );
            } else {
                $table->primary(
                    [
                        PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type',
                    ],
                    'model_has_permissions_permission_model_type_primary',
                );
            }
        });
    }

    /**
     * @param array<string, string> $tableNames
     * @param array<string, string> $columnNames
     */
    private function createModelHasRolesTable(
        string $name,
        array $tableNames,
        array $columnNames,
        bool $teams,
    ): void {
        Schema::create($name, static function (Blueprint $table) use ($tableNames, $columnNames, $teams): void {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index(
                [
                    $columnNames['model_morph_key'], 'model_type',
                ], 
                'model_has_roles_model_id_model_type_index',
            );

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [
                        $columnNames['team_foreign_key'],
                        PermissionRegistrar::$pivotRole,
                        $columnNames['model_morph_key'],
                        'model_type',
                    ],
                    'model_has_roles_role_model_type_primary',
                );
            } else {
                $table->primary(
                    [
                        PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type',
                    ],
                    'model_has_roles_role_model_type_primary',
                );
            }
        });
    }

    /**
     * @param array<string, string> $tableNames
     */
    private function createRoleHasPermissionsTable(string $name, array $tableNames): void
    {
        Schema::create($name, static function (Blueprint $table) use ($tableNames): void {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([PermissionRegistrar::$pivotPermission, PermissionRegistrar::$pivotRole],
                            'role_has_permissions_permission_id_role_id_primary');
        });
    }
}
