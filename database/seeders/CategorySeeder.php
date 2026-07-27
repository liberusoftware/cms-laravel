<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'General',
            'Guides',
            'Updates',
        ];

        $blog = Collection::firstOrFail();
        $team = Team::first();

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'collection_id' => $blog->id,
                'team_id' => $team?->id,
            ]);
        }

    }
}
