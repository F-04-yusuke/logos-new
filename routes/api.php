<?php

use App\Http\Controllers\Api\TopicApiController;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// 公開API（認証不要）
Route::get('/topics', [TopicApiController::class, 'index']);
Route::get('/topics/{topic}', [TopicApiController::class, 'show']);
Route::get('/categories', fn() => response()->json(
    Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get()
));

// 新規登録
Route::post('/register', function (Request $request) {
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user  = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);
    $token = $user->createToken('next-app')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'avatar'                    => $user->avatar ?? null,
            'is_pro'                    => $user->is_pro,
            'is_admin'                  => $user->is_admin,
            'unread_notifications_count' => 0,
        ],
    ], 201);
});

// ログイン（トークン発行）
Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
    }

    $user  = Auth::user();
    $token = $user->createToken('next-app')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'avatar'                    => $user->avatar ?? null,
            'is_pro'                    => $user->is_pro,
            'is_admin'                  => $user->is_admin,
            'unread_notifications_count' => Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
        ],
    ]);
});

// 認証済みルートグループ
Route::middleware('auth:sanctum')->group(function () {

    // ログアウト（トークン削除）
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ログアウトしました']);
    });

    // 通知一覧
    Route::get('/notifications', function (Request $request) {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user->id)
            ->with('actor:id,name,avatar')
            ->latest()
            ->paginate(20);

        $items = collect($notifications->items())->map(function ($n) {
            $topicId = null;
            if ($n->notifiable_type === 'App\\Models\\Post') {
                $topicId = \App\Models\Post::find($n->notifiable_id)?->topic_id;
            } elseif ($n->notifiable_type === 'App\\Models\\Topic') {
                $topicId = $n->notifiable_id;
            } elseif ($n->notifiable_type === 'App\\Models\\Comment') {
                $topicId = \App\Models\Comment::find($n->notifiable_id)?->topic_id;
            }
            return [
                'id'         => $n->id,
                'type'       => $n->type,
                'text'       => $n->text,
                'is_unread'  => $n->isUnread(),
                'created_at' => $n->created_at,
                'topic_id'   => $topicId,
                'actor'      => $n->actor ? ['id' => $n->actor->id, 'name' => $n->actor->name, 'avatar' => $n->actor->avatar] : null,
            ];
        });

        return response()->json([
            'data'         => $items,
            'current_page' => $notifications->currentPage(),
            'last_page'    => $notifications->lastPage(),
            'has_unread'   => Notification::where('user_id', $user->id)->whereNull('read_at')->exists(),
        ]);
    });

    // 通知を全て既読
    Route::patch('/notifications/read-all', function (Request $request) {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['message' => '既読にしました']);
    });

    // 通知を1件既読
    Route::patch('/notifications/{notification}/read', function (Request $request, Notification $notification) {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $notification->markAsRead();
        return response()->json(['message' => '既読にしました']);
    });

    // いいね一覧（投稿・コメント）
    Route::get('/user/likes', function (Request $request) {
        $user = $request->user();

        $likedPostIds = \App\Models\Like::where('user_id', $user->id)->pluck('post_id');
        $likedPosts = \App\Models\Post::whereIn('id', $likedPostIds)
            ->where('is_published', true)
            ->with(['user:id,name', 'topic:id,title'])
            ->withCount('likes')
            ->latest()
            ->get();

        $likedComments = $user->likedComments()
            ->with(['user:id,name', 'topic:id,title'])
            ->withCount('likes')
            ->latest()
            ->get();

        return response()->json([
            'posts'    => $likedPosts,
            'comments' => $likedComments,
        ]);
    });

    // 認証済みユーザー確認用
    Route::get('/user/me', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'avatar'                    => $user->avatar ?? null,
            'is_pro'                    => $user->is_pro,
            'is_admin'                  => $user->is_admin,
            'unread_notifications_count' => Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
        ]);
    });

    // トピック作成（PRO限定）
    Route::post('/topics', [TopicApiController::class, 'store']);

    // エビデンス投稿
    Route::post('/topics/{topic}/posts', function (Request $request, Topic $topic) {
        $data = $request->validate([
            'url'          => 'required|url|max:2048',
            'category'     => 'required|string|in:YouTube,X,記事,知恵袋,本,その他',
            'comment'      => 'nullable|string|max:5000',
            'is_published' => 'boolean',
        ]);

        $post = new \App\Models\Post();
        $post->user_id      = $request->user()->id;
        $post->topic_id     = $topic->id;
        $post->url          = $data['url'];
        $post->category     = $data['category'];
        $post->comment      = $data['comment'] ?? null;
        $post->is_published = $data['is_published'] ?? true;
        $post->save();

        $post->load('user:id,name');
        $post->loadCount('likes');

        return response()->json($post, 201);
    });

    // コメント投稿（1人1件制限）
    Route::post('/topics/{topic}/comments', function (Request $request, Topic $topic) {
        $exists = $topic->comments()
            ->where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'すでにコメントを投稿済みです'], 422);
        }

        $data = $request->validate(['body' => 'required|string|max:10000']);

        $comment = \App\Models\Comment::create([
            'user_id'  => $request->user()->id,
            'topic_id' => $topic->id,
            'body'     => $data['body'],
        ]);

        $comment->load('user:id,name');
        $comment->loadCount('likes');
        $comment->setRelation('replies', collect());

        return response()->json($comment, 201);
    });

    // エビデンスいいね（トグル）
    Route::post('/posts/{post}/like', function (Request $request, \App\Models\Post $post) {
        $user = $request->user();
        $like = $post->likes()->where('user_id', $user->id)->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }
        return response()->json(['liked' => $liked, 'likes_count' => $post->likes()->count()]);
    });

    // コメントいいね（トグル）
    Route::post('/comments/{comment}/like', function (Request $request, \App\Models\Comment $comment) {
        $user   = $request->user();
        $liked  = $comment->likes()->where('user_id', $user->id)->exists();
        if ($liked) {
            $comment->likes()->detach($user->id);
            $liked = false;
        } else {
            $comment->likes()->attach($user->id);
            $liked = true;
        }
        return response()->json(['liked' => $liked, 'likes_count' => $comment->likes()->count()]);
    });

    // ブックマーク（トグル）
    Route::post('/topics/{topic}/bookmark', function (Request $request, Topic $topic) {
        $user        = $request->user();
        $isBookmarked = $topic->isSavedBy($user);
        if ($isBookmarked) {
            \DB::table('bookmarks')->where('user_id', $user->id)->where('topic_id', $topic->id)->delete();
            $bookmarked = false;
        } else {
            \DB::table('bookmarks')->insert(['user_id' => $user->id, 'topic_id' => $topic->id, 'created_at' => now(), 'updated_at' => now()]);
            $bookmarked = true;
        }
        return response()->json(['bookmarked' => $bookmarked]);
    });

    // 閲覧履歴
    Route::get('/history', function (Request $request) {
        $user = $request->user();

        $topics = $user->viewedTopics()
            ->with('categories:id,name')
            ->paginate(12);

        $items = $topics->getCollection()->map(fn($topic) => [
            'id'             => $topic->id,
            'title'          => $topic->title,
            'categories'     => $topic->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'last_viewed_at' => $topic->pivot->last_viewed_at,
        ]);

        return response()->json([
            'data'         => $items,
            'current_page' => $topics->currentPage(),
            'last_page'    => $topics->lastPage(),
        ]);
    });

    // プロフィール取得（name_updated_at付き）
    Route::get('/profile', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id'              => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'avatar'          => $user->avatar ?? null,
            'is_pro'          => $user->is_pro,
            'is_admin'        => $user->is_admin,
            'name_updated_at' => $user->name_updated_at,
        ]);
    });

    // プロフィール更新（multipart/form-data）
    Route::post('/profile', function (Request $request) {
        $user = $request->user();

        $canChangeName = !$user->name_updated_at ||
            \Carbon\Carbon::parse($user->name_updated_at)->addDays(7)->isPast();

        $rules = [
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
        ];
        if ($canChangeName) {
            $rules['name'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->email = $data['email'];

        if ($canChangeName && isset($data['name']) && $data['name'] !== $user->name) {
            $user->name            = $data['name'];
            $user->name_updated_at = now();
        }

        $user->save();

        return response()->json([
            'message' => '保存しました',
            'user'    => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'avatar'          => $user->avatar ?? null,
                'name_updated_at' => $user->name_updated_at,
            ],
        ]);
    });

    // パスワード更新
    Route::put('/profile/password', function (Request $request) {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['errors' => ['current_password' => ['現在のパスワードが正しくありません']]], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(['message' => 'パスワードを更新しました']);
    });

    // アカウント削除
    Route::delete('/profile', function (Request $request) {
        $data = $request->validate(['password' => 'required|string']);

        $user = $request->user();

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json(['errors' => ['password' => ['パスワードが正しくありません']]], 422);
        }

        $request->user()->currentAccessToken()->delete();
        $user->delete();

        return response()->json(['message' => 'アカウントを削除しました']);
    });

    // ダッシュボード
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();

        $posts = $user->posts()
            ->where('is_published', true)
            ->with(['topic:id,title', 'user:id,name,avatar'])
            ->withCount('likes')
            ->latest()
            ->get();

        $drafts = $user->posts()
            ->where('is_published', false)
            ->with(['topic:id,title', 'user:id,name,avatar'])
            ->withCount('likes')
            ->latest()
            ->get();

        $comments = $user->comments()
            ->whereNull('parent_id')
            ->with([
                'user:id,name,avatar',
                'topic:id,title',
                'replies' => function ($q) { $q->oldest()->with('user:id,name,avatar')->withCount('likes'); },
            ])
            ->withCount('likes')
            ->latest()
            ->get();

        $topics = $user->topics()
            ->latest()
            ->get(['id', 'title', 'created_at']);

        return response()->json([
            'posts'       => $posts,
            'drafts'      => $drafts,
            'draft_count' => $drafts->count(),
            'comments'    => $comments,
            'analyses'    => [],
            'topics'      => $topics,
        ]);
    });

    // 投稿削除（自分の投稿のみ）
    Route::delete('/posts/{post}', function (Request $request, \App\Models\Post $post) {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $post->delete();
        return response()->json(['message' => '削除しました']);
    });

    // トピック削除（自分のトピックのみ）
    Route::delete('/topics/{topic}', function (Request $request, Topic $topic) {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $topic->delete();
        return response()->json(['message' => '削除しました']);
    });

    // カテゴリ管理（管理者専用）
    Route::post('/categories', function (Request $request) {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'required|integer',
            'parent_id'  => 'nullable|integer|exists:categories,id',
        ]);
        $category = Category::create($data);
        return response()->json($category, 201);
    });

    Route::patch('/categories/{category}', function (Request $request, Category $category) {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'required|integer',
        ]);
        $category->update($data);
        return response()->json($category);
    });

    Route::delete('/categories/{category}', function (Request $request, Category $category) {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $category->delete();
        return response()->json(['message' => '削除しました']);
    });
});
