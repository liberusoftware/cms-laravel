<?php

namespace Tests\Unit;

use App\Models\Collection;
use App\Models\CollectionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_collection_can_be_created(): void
    {
        Collection::factory()->create([
            'name' => 'Blog',
            'slug' => 'blog',
        ]);

        $this->assertDatabaseHas('collections', [
            'name' => 'Blog',
            'slug' => 'blog',
        ]);
    }

    public function test_collection_has_fillable_attributes(): void
    {
        $collection = new Collection;

        $this->assertContains('name', $collection->getFillable());
        $this->assertContains('slug', $collection->getFillable());
    }

    public function test_collection_has_many_items(): void
    {
        $collection = new Collection;

        $this->assertInstanceOf(
            HasMany::class,
            $collection->items()
        );
    }

    public function test_collection_has_many_categories(): void
    {
        $collection = new Collection;

        $this->assertInstanceOf(
            HasMany::class,
            $collection->categories()
        );
    }

    public function test_collection_belongs_to_team(): void
    {
        $collection = new Collection;

        $this->assertInstanceOf(
            BelongsTo::class,
            $collection->team()
        );
    }

    public function test_collection_items_relationship_returns_items(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create();
        CollectionItem::factory()->count(3)->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $collection->items);
    }
}
