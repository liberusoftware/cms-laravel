<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Forms\Models\Form;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Posts\Models\Category;
use Liberu\Cms\Posts\Models\Post;

/**
 * Seeds a small set of published content so the public site, Delivery API,
 * search, and forms have something to show out of the box. Idempotent — safe to
 * re-run.
 */
final class CmsDemoSeeder extends Seeder
{
    public function run(): void
    {
        Page::query()->firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'template' => 'home',
                'excerpt' => 'Welcome to the Liberu CMS demo site.',
                'content' => '<p>Welcome to the Liberu CMS demo. This home page is served from the cms-pages module.</p>',
                'status' => WorkflowState::Published->value,
                'published_at' => now(),
            ],
        );

        Page::query()->firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'template' => 'default',
                'excerpt' => 'A little about this demo CMS.',
                'content' => '<p>This About page is a published <strong>Page</strong> you can fetch via the Delivery API and find through search.</p>',
                'status' => WorkflowState::Published->value,
                'published_at' => now(),
            ],
        );

        $category = Category::query()->firstOrCreate(['slug' => 'news'], ['name' => 'News']);

        $post = Post::query()->firstOrCreate(
            ['slug' => 'hello-world'],
            [
                'title' => 'Hello World',
                'excerpt' => 'The first post on the demo blog.',
                'content' => '<p>This is a published <strong>Post</strong>, complete with a category.</p>',
                'status' => WorkflowState::Published->value,
                'published_at' => now(),
            ],
        );
        $post->categories()->syncWithoutDetaching([$category->id]);

        ContentType::query()->firstOrCreate(
            ['key' => 'faq'],
            [
                'name' => 'FAQ',
                'singular_label' => 'FAQ',
                'plural_label' => 'FAQs',
                'fields' => [
                    ['name' => 'question', 'label' => 'Question', 'type' => 'text', 'required' => true],
                    ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'required' => true],
                ],
            ],
        );

        Form::query()->firstOrCreate(
            ['slug' => 'contact'],
            [
                'name' => 'Contact',
                'fields' => [
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true],
                ],
            ],
        );
    }
}
