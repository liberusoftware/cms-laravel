<?php

namespace Tests\Unit;

use App\Models\Collection;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_can_be_created(): void
    {
        $collection = Collection::factory()->create();
        Tag::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'collection_id' => $collection->id,
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);
    }

    public function test_tag_has_fillable_attributes(): void
    {
        $tag = new Tag;

        $this->assertContains('name', $tag->getFillable());
        $this->assertContains('slug', $tag->getFillable());
        $this->assertContains('collection_id', $tag->getFillable());
        $this->assertContains('description', $tag->getFillable());
    }

    public function test_tag_belongs_to_collection(): void
    {
        $tag = new Tag;

        $this->assertInstanceOf(
            BelongsTo::class,
            $tag->collection()
        );
    }

    public function test_tag_belongs_to_team(): void
    {
        $tag = new Tag;

        $this->assertInstanceOf(
            BelongsTo::class,
            $tag->team()
        );
    }

    public function test_tag_collection_relationship_returns_correct_collection(): void
    {
        $collection = Collection::factory()->create(['name' => 'Blog']);
        $tag = Tag::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'collection_id' => $collection->id,
        ]);

        $this->assertEquals('Blog', $tag->collection->name);
    }
}
