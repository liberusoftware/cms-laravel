<?php

namespace Tests\Unit;

use App\Models\Collection;
use App\Models\CollectionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CollectionItemModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_collection_item_can_be_created(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create();
        CollectionItem::factory()->create([
            'title' => 'My First Post',
            'slug' => 'my-first-post',
            'collection_id' => $collection->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('collection_items', [
            'title' => 'My First Post',
            'slug' => 'my-first-post',
        ]);
    }

    public function test_collection_item_has_fillable_attributes(): void
    {
        $item = new CollectionItem;

        $this->assertContains('title', $item->getFillable());
        $this->assertContains('slug', $item->getFillable());
        $this->assertContains('content', $item->getFillable());
        $this->assertContains('status', $item->getFillable());
        $this->assertContains('collection_id', $item->getFillable());
        $this->assertContains('user_id', $item->getFillable());
    }

    public function test_collection_item_belongs_to_collection(): void
    {
        $item = new CollectionItem;

        $this->assertInstanceOf(
            BelongsTo::class,
            $item->collection()
        );
    }

    public function test_collection_item_belongs_to_team(): void
    {
        $item = new CollectionItem;

        $this->assertInstanceOf(
            BelongsTo::class,
            $item->team()
        );
    }

    public function test_collection_item_published_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create();
        $item = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'published_at' => '2025-01-01 00:00:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $item->fresh()->published_at);
    }

    public function test_collection_item_collection_relationship_returns_correct_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create(['name' => 'Blog']);
        $item = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Blog', $item->collection->name);
    }
}
