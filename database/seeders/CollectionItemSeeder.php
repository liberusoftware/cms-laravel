<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\CollectionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class CollectionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrFail();
        $blog = Collection::firstOrFail();

        CollectionItem::factory(24)->create([
            'collection_id' => $blog->id,
            'user_id' => $user->id,
        ]);

    }
}
