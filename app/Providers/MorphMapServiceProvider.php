<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\{
    User,
    Profile,
};

class MorphMapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'profile' => Profile::class,
        ]);
    }
}
