<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created(): void
    {
        $collection = Collection::factory()->create();
        Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'collection_id' => $collection->id,
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Tech',
            'slug' => 'tech',
        ]);
    }

    public function test_category_has_fillable_attributes(): void
    {
        $category = new Category;

        $this->assertContains('name', $category->getFillable());
        $this->assertContains('slug', $category->getFillable());
        $this->assertContains('collection_id', $category->getFillable());
        $this->assertContains('description', $category->getFillable());
    }

    public function test_category_belongs_to_collection(): void
    {
        $category = new Category;

        $this->assertInstanceOf(
            BelongsTo::class,
            $category->collection()
        );
    }

    public function test_category_belongs_to_team(): void
    {
        $category = new Category;

        $this->assertInstanceOf(
            BelongsTo::class,
            $category->team()
        );
    }

    public function test_category_collection_relationship_returns_correct_collection(): void
    {
        $collection = Collection::factory()->create(['name' => 'Blog']);
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'collection_id' => $collection->id,
        ]);

        $this->assertEquals('Blog', $category->collection->name);
    }
}
