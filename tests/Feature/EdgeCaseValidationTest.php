<?php

namespace Tests\Feature;

use App\Models\Analysis;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * フェーズ1 エッジケース検証テスト
 *
 * 修正した以下の箇所が正しく動作するかを確認する:
 *  1. AnalysisController::update  — バリデーション追加
 *  2. analyses.destroy ルート     — auth ミドルウェア追加
 *  3. PostController::update      — バリデーション追加
 *  4. PostController::store       — comment/url 上限
 *  5. TopicController::store/update — content/timeline 上限
 *  6. AnalysisController::aiAssist — prompt/context 上限
 *  7. Post 下書き機能              — is_published フラグ制御
 */
class EdgeCaseValidationTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // 1. AnalysisController::update
    // =========================================================

    /** data が送られてこない場合は 422 */
    public function test_analysis_update_requires_data(): void
    {
        $user     = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->patchJson(route('analyses.update', $analysis), [
                'title' => '新しいタイトル',
                // data を故意に省略
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['data']);
    }

    /** data に配列以外（文字列）を渡した場合は 422 */
    public function test_analysis_update_data_must_be_array(): void
    {
        $user     = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->patchJson(route('analyses.update', $analysis), [
                'data' => 'これは文字列なので配列ではない',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['data']);
    }

    /** title が 255 文字超の場合は 422 */
    public function test_analysis_update_title_max_255(): void
    {
        $user     = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->patchJson(route('analyses.update', $analysis), [
                'title' => str_repeat('あ', 256),
                'data'  => ['nodes' => []],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    /** 正常データは保存成功する */
    public function test_analysis_update_valid_data_saves(): void
    {
        $user     = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->patchJson(route('analyses.update', $analysis), [
                'title' => '更新後タイトル',
                'data'  => ['nodes' => [['id' => 1, 'label' => 'root']]],
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('analyses', [
            'id'    => $analysis->id,
            'title' => '更新後タイトル',
        ]);
    }

    /** 他人の Analysis は更新できない（403） */
    public function test_analysis_update_forbidden_for_other_user(): void
    {
        $owner    = User::factory()->create();
        $other    = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)
            ->patchJson(route('analyses.update', $analysis), [
                'data' => ['nodes' => []],
            ]);

        $response->assertStatus(403);
    }

    // =========================================================
    // 2. analyses.destroy — auth ミドルウェア
    // =========================================================

    /** 未ログインでの削除リクエストはログイン画面へリダイレクト（302）*/
    public function test_analysis_destroy_requires_auth(): void
    {
        $user     = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson(route('analyses.destroy', $analysis));

        // auth ミドルウェアにより 401 が返る（JSON リクエストの場合）
        $response->assertStatus(401);
    }

    /** 他人の Analysis は削除できない（403） */
    public function test_analysis_destroy_forbidden_for_other_user(): void
    {
        $owner    = User::factory()->create();
        $other    = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)
            ->delete(route('analyses.destroy', $analysis));

        $response->assertStatus(403);
        $this->assertDatabaseHas('analyses', ['id' => $analysis->id]);
    }

    /** オーナーは削除できる */
    public function test_analysis_destroy_succeeds_for_owner(): void
    {
        $user     = User::factory()->create();
        $analysis = Analysis::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete(route('analyses.destroy', $analysis));

        $response->assertRedirect();
        $this->assertDatabaseMissing('analyses', ['id' => $analysis->id]);
    }

    // =========================================================
    // 3. PostController::update — バリデーション
    // =========================================================

    /** url が空の場合は 422（下書きポストで検証） */
    public function test_post_update_requires_url(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create(['user_id' => $user->id, 'topic_id' => $topic->id, 'is_published' => false]);

        $response = $this->actingAs($user)
            ->patch(route('posts.update', $post), [
                'url'          => '',
                'category'     => 'Article',
                'is_published' => '0',
            ]);

        $response->assertSessionHasErrors(['url']);
    }

    /** 不正な URL 形式は 422 */
    public function test_post_update_rejects_invalid_url(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create(['user_id' => $user->id, 'topic_id' => $topic->id, 'is_published' => false]);

        $response = $this->actingAs($user)
            ->patch(route('posts.update', $post), [
                'url'          => 'これはURLではない',
                'category'     => 'Article',
                'is_published' => '0',
            ]);

        $response->assertSessionHasErrors(['url']);
    }

    /** url が 2048 文字超の場合は 422 */
    public function test_post_update_rejects_url_over_2048_chars(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create(['user_id' => $user->id, 'topic_id' => $topic->id, 'is_published' => false]);

        $longUrl = 'https://example.com/' . str_repeat('a', 2030);

        $response = $this->actingAs($user)
            ->patch(route('posts.update', $post), [
                'url'          => $longUrl,
                'category'     => 'Article',
                'is_published' => '0',
            ]);

        $response->assertSessionHasErrors(['url']);
    }

    /** comment が 2000 文字超の場合は 422 */
    public function test_post_update_rejects_comment_over_2000_chars(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create(['user_id' => $user->id, 'topic_id' => $topic->id, 'is_published' => false]);

        $response = $this->actingAs($user)
            ->patch(route('posts.update', $post), [
                'url'          => 'https://example.com',
                'category'     => 'Article',
                'comment'      => str_repeat('あ', 2001),
                'is_published' => '0',
            ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /** 下書きの正常データは保存できる */
    public function test_post_update_valid_data_saves(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create(['user_id' => $user->id, 'topic_id' => $topic->id, 'is_published' => false]);

        $response = $this->actingAs($user)
            ->patch(route('posts.update', $post), [
                'url'          => 'https://example.com/updated',
                'category'     => 'YouTube',
                'comment'      => '修正後のコメント',
                'is_published' => '0',
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('posts', [
            'id'  => $post->id,
            'url' => 'https://example.com/updated',
        ]);
    }

    /** 他人の Post は更新できない（403） */
    public function test_post_update_forbidden_for_other_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $owner->id]);
        $post  = Post::factory()->create(['user_id' => $owner->id, 'topic_id' => $topic->id, 'is_published' => false]);

        $response = $this->actingAs($other)
            ->patch(route('posts.update', $post), [
                'url'          => 'https://example.com',
                'category'     => 'Article',
                'is_published' => '0',
            ]);

        $response->assertStatus(403);
    }

    // =========================================================
    // 4. PostController::store — comment 上限
    // =========================================================

    /** store で comment が 2000 文字超の場合は 422 */
    public function test_post_store_rejects_comment_over_2000_chars(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('posts.store', $topic), [
                'url'      => 'https://example.com',
                'category' => 'Article',
                'comment'  => str_repeat('あ', 2001),
            ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /** store で url が 2048 文字超の場合は 422 */
    public function test_post_store_rejects_url_over_2048_chars(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);

        $longUrl = 'https://example.com/' . str_repeat('x', 2030);

        $response = $this->actingAs($user)
            ->post(route('posts.store', $topic), [
                'url'      => $longUrl,
                'category' => 'Article',
            ]);

        $response->assertSessionHasErrors(['url']);
    }

    // =========================================================
    // 5. TopicController::store / update — content 上限
    // =========================================================

    /** store で content が 20000 文字超の場合は 422 */
    public function test_topic_store_rejects_content_over_20000_chars(): void
    {
        $user = User::factory()->create();

        // category が必要なので作成
        $category = \App\Models\Category::create([
            'name'       => 'テストカテゴリ',
            'parent_id'  => null,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)
            ->post(route('topics.store'), [
                'title'        => 'テストトピック',
                'content'      => str_repeat('あ', 20001),
                'category_ids' => [$category->id],
            ]);

        $response->assertSessionHasErrors(['content']);
    }

    /** update で content が 20000 文字超の場合は 422 */
    public function test_topic_update_rejects_content_over_20000_chars(): void
    {
        $user     = User::factory()->create();
        $topic    = Topic::factory()->create(['user_id' => $user->id]);
        $category = \App\Models\Category::create([
            'name'       => 'テストカテゴリ',
            'parent_id'  => null,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)
            ->patch(route('topics.update', $topic), [
                'title'        => 'タイトル',
                'content'      => str_repeat('あ', 20001),
                'category_ids' => [$category->id],
            ]);

        $response->assertSessionHasErrors(['content']);
    }

    /** update で timeline_event の1行が 500 文字超の場合は 422 */
    public function test_topic_update_rejects_timeline_event_over_500_chars(): void
    {
        $user     = User::factory()->create();
        $topic    = Topic::factory()->create(['user_id' => $user->id]);
        $category = \App\Models\Category::create([
            'name'       => 'テストカテゴリ',
            'parent_id'  => null,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)
            ->patch(route('topics.update', $topic), [
                'title'          => 'タイトル',
                'content'        => '本文',
                'category_ids'   => [$category->id],
                'timeline_date'  => ['2024年01月'],
                'timeline_event' => [str_repeat('あ', 501)],
            ]);

        $response->assertSessionHasErrors(['timeline_event.0']);
    }

    // =========================================================
    // 6. AnalysisController::aiAssist — prompt/context 上限
    // =========================================================

    /** prompt が 5000 文字超の場合は 422 */
    public function test_ai_assist_rejects_prompt_over_5000_chars(): void
    {
        $user = User::factory()->create(['is_pro' => true]);

        $response = $this->actingAs($user)
            ->postJson(route('tools.ai_assist'), [
                'prompt' => str_repeat('あ', 5001),
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['prompt']);
    }

    /** context が 10000 文字超の場合は 422 */
    public function test_ai_assist_rejects_context_over_10000_chars(): void
    {
        $user = User::factory()->create(['is_pro' => true]);

        $response = $this->actingAs($user)
            ->postJson(route('tools.ai_assist'), [
                'prompt'  => '有効なプロンプト',
                'context' => str_repeat('あ', 10001),
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['context']);
    }

    /** prompt が空の場合は 422 */
    public function test_ai_assist_requires_prompt(): void
    {
        $user = User::factory()->create(['is_pro' => true]);

        $response = $this->actingAs($user)
            ->postJson(route('tools.ai_assist'), [
                'context' => '何か文脈',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['prompt']);
    }

    /** 未ログインでは ai-assist にアクセスできない */
    public function test_ai_assist_requires_auth(): void
    {
        $response = $this->postJson(route('tools.ai_assist'), [
            'prompt' => 'テスト',
        ]);

        $response->assertStatus(401);
    }

    /** 無料会員は ai-assist にアクセスできない（403） */
    public function test_ai_assist_requires_pro(): void
    {
        $user = User::factory()->create(['is_pro' => false]);

        $response = $this->actingAs($user)
            ->postJson(route('tools.ai_assist'), [
                'prompt' => 'テスト',
            ]);

        $response->assertStatus(403);
    }

    // =========================================================
    // 7. Post 下書き機能（is_published）
    // =========================================================

    /** is_published=false で保存すると下書きになる */
    public function test_post_store_saves_as_draft(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('posts.store', $topic), [
                'url'          => 'https://example.com/draft',
                'category'     => 'Article',
                'is_published' => '0',
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('posts', [
            'url'          => 'https://example.com/draft',
            'is_published' => false,
        ]);
    }

    /** is_published=true（デフォルト）で保存すると公開済みになる */
    public function test_post_store_saves_as_published(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('posts.store', $topic), [
                'url'          => 'https://example.com/published',
                'category'     => 'Article',
                'is_published' => '1',
            ]);

        $response->assertRedirect(route('topics.show', $topic));
        $this->assertDatabaseHas('posts', [
            'url'          => 'https://example.com/published',
            'is_published' => true,
        ]);
    }

    /** 下書きは edit 画面にアクセスできる */
    public function test_post_edit_allowed_for_draft(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create([
            'user_id'      => $user->id,
            'topic_id'     => $topic->id,
            'is_published' => false,
        ]);

        $response = $this->actingAs($user)->get(route('posts.edit', $post));
        $response->assertStatus(200);
    }

    /** 公開済み投稿は edit 画面にアクセスできない（403） */
    public function test_post_edit_forbidden_for_published(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create([
            'user_id'      => $user->id,
            'topic_id'     => $topic->id,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)->get(route('posts.edit', $post));
        $response->assertStatus(403);
    }

    /** 下書きを本投稿（is_published=true）に変更できる */
    public function test_post_update_can_publish_draft(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create([
            'user_id'      => $user->id,
            'topic_id'     => $topic->id,
            'is_published' => false,
        ]);

        $response = $this->actingAs($user)
            ->patch(route('posts.update', $post), [
                'url'          => 'https://example.com/now-public',
                'category'     => 'Article',
                'is_published' => '1',
            ]);

        $response->assertRedirect(route('topics.show', $post->topic_id));
        $this->assertDatabaseHas('posts', [
            'id'           => $post->id,
            'is_published' => true,
        ]);
    }

    /** 公開済み投稿は update できない（403） */
    public function test_post_update_forbidden_for_published(): void
    {
        $user  = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);
        $post  = Post::factory()->create([
            'user_id'      => $user->id,
            'topic_id'     => $topic->id,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)
            ->patch(route('posts.update', $post), [
                'url'          => 'https://example.com/try-edit',
                'category'     => 'Article',
                'is_published' => '1',
            ]);

        $response->assertStatus(403);
    }

    /** is_published=true の投稿はトピック詳細に表示される */
    public function test_topic_show_only_displays_published_posts(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $owner->id]);

        // 公開投稿
        Post::factory()->create([
            'user_id'      => $other->id,
            'topic_id'     => $topic->id,
            'is_published' => true,
            'url'          => 'https://example.com/public',
        ]);
        // 下書き投稿（トピック詳細には出ないはず）
        Post::factory()->create([
            'user_id'      => $other->id,
            'topic_id'     => $topic->id,
            'is_published' => false,
            'url'          => 'https://example.com/draft-hidden',
        ]);

        $response = $this->actingAs($owner)->get(route('topics.show', $topic));
        $response->assertStatus(200);

        // コントローラーが返す $posts に下書きが含まれていないことを確認
        $posts = $response->viewData('posts');
        $this->assertTrue($posts->every(fn ($p) => $p->is_published));
        $this->assertEquals(1, $posts->count());
    }
}
