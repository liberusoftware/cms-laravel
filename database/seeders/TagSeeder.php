<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Tag;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'General',
            'Tutorial',
            'Update',
            'Opinion',
        ];

        $blog = Collection::firstOrFail();
        $team = Team::first();

        foreach ($tags as $name) {
            Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'collection_id' => $blog->id,
                'team_id' => $team?->id,
            ]);
        }

    }
}
