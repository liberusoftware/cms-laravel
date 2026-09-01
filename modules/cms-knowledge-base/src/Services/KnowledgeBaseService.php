<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBase\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\KnowledgeBase\Models\KnowledgeArticle;
use Liberu\Cms\KnowledgeBase\Models\KnowledgeArticleVersion;
use Liberu\Cms\KnowledgeBase\Models\KnowledgeFeedback;

final class KnowledgeBaseService
{
    public function create(string $slug, string $title, string $body, ?int $teamId = null, ?int $parentId = null): KnowledgeArticle
    {
        $this->text($slug, 'slug', 180);
        $this->text($title, 'title', 240);
        $this->text($body, 'body', 100000);
        if ($parentId !== null && ! KnowledgeArticle::query()->whereKey($parentId)->where('team_id', $teamId)->exists()) {
            throw ValidationException::withMessages(['parent_id' => 'The parent article is invalid for this tenant.']);
        }

        return KnowledgeArticle::query()->create(['team_id' => $teamId, 'public_id' => (string) Str::uuid(), 'parent_id' => $parentId, 'slug' => $slug, 'title' => $title, 'body' => $body, 'status' => 'draft']);
    }

    public function version(KnowledgeArticle $article, string $body, string $authorKey): KnowledgeArticleVersion
    {
        $this->text($body, 'body', 100000);
        $this->text($authorKey, 'author_key', 180);
        $latest = KnowledgeArticleVersion::query()->where('article_id', $article->id)->orderByDesc('version')->first();
        $next = $latest === null ? 1 : $latest->version + 1;

        return KnowledgeArticleVersion::query()->create(['article_id' => $article->id, 'version' => $next, 'body' => $body, 'author_key' => $authorKey]);
    }

    public function publish(KnowledgeArticle $article, ?int $teamId = null): KnowledgeArticle
    {
        $this->tenant($article, $teamId);
        $article->update(['status' => 'published', 'published_at' => now()]);

        return $article->refresh();
    }

    public function feedback(KnowledgeArticle $article, bool $helpful, ?string $comment = null, ?string $reporterKey = null, ?int $teamId = null): KnowledgeFeedback
    {
        $this->tenant($article, $teamId);
        if ($comment !== null) {
            $this->text($comment, 'comment', 1000);
        } if ($reporterKey !== null) {
            $this->text($reporterKey, 'reporter_key', 180);
        }

        return KnowledgeFeedback::query()->create(['article_id' => $article->id, 'team_id' => $teamId, 'helpful' => $helpful, 'comment' => $comment, 'reporter_key' => $reporterKey]);
    }

    /** @return list<KnowledgeArticle> */
    public function related(KnowledgeArticle $article, ?int $teamId = null): array
    {
        $this->tenant($article, $teamId);

        return array_values(KnowledgeArticle::query()->where('team_id', $teamId)->where('status', 'published')->where('id', '<>', $article->id)->orderByDesc('search_weight')->limit(10)->get()->all());
    }

    private function tenant(KnowledgeArticle $article, ?int $teamId): void
    {
        if ($article->team_id !== $teamId) {
            throw ValidationException::withMessages(['team_id' => 'The article belongs to another tenant.']);
        }
    }

    private function text(string $value, string $field, int $max): void
    {
        if (trim($value) === '' || strlen($value) > $max || str_contains($value, "\0")) {
            throw ValidationException::withMessages([$field => 'The value is invalid.']);
        }
    }
}
