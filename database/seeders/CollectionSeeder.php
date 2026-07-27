<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Team;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Collection::create([
            'name' => 'Blog',
            'slug' => 'blog',
            'team_id' => Team::first()?->id,
        ]);
    }
}
