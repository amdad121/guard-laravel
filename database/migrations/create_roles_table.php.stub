<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Facades\Guard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = config('guard.tables');
        $models = config('guard.models');
        $pivotTableName = Guard::getPivotTableName(Arr::only($models, ['role', 'user']));

        Schema::create($tables['roles'], function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create($pivotTableName, function (Blueprint $table) use ($tables, $models) {
            $table->foreignId(Guard::getSingularName($tables['roles']).'_id')->constrained($tables['roles'])->cascadeOnDelete();
            $table->foreignId(Guard::getSingularName(Guard::getTableName($models['user'])).'_id')->constrained(Guard::getTableName($models['user']))->cascadeOnDelete();
            $table->primary([Guard::getSingularName($tables['roles']).'_id', Guard::getSingularName(Guard::getTableName($models['user'])).'_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = config('guard.tables');
        $models = config('guard.models');
        $pivotTableName = Guard::getPivotTableName(Arr::only($models, ['role', 'user']));

        Schema::dropIfExists($tables['roles']);
        Schema::dropIfExists($pivotTableName);
    }
};
