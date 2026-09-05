<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_blog_index_lists_published_posts(): void
    {
        $featured = BlogPost::query()->published()->latest('published_at')->firstOrFail();

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Expert Car Care Guides', false)
            ->assertSee('Featured Article', false)
            ->assertSee($featured->title, false);
    }

    public function test_blog_show_page_renders_content(): void
    {
        $post = BlogPost::query()->published()->firstOrFail();

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->excerpt);
    }

    public function test_header_footer_and_home_surface_blog(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertSee('From the Blog', false);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee(route('blog.index'), false);
    }

    public function test_draft_posts_are_hidden_from_public(): void
    {
        $post = BlogPost::query()->published()->firstOrFail();
        $post->update(['status' => 'draft']);

        $this->get(route('blog.show', $post->slug))->assertNotFound();
        $this->get(route('blog.index'))->assertDontSee($post->title, false);
    }

    public function test_admin_can_publish_post_and_it_appears_publicly(): void
    {
        $admin = User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.blog-posts.store'), [
                'title' => 'Fresh Coat Care Guide',
                'slug' => 'fresh-coat-care-guide',
                'excerpt' => 'How to maintain a fresh ceramic coat.',
                'content' => '<p>Wash gently and avoid abrasive towels.</p>',
                'status' => 'published',
                'blog_category_id' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('blog_posts', [
            'slug' => 'fresh-coat-care-guide',
            'status' => 'published',
        ]);

        $this->get(route('blog.show', 'fresh-coat-care-guide'))
            ->assertOk()
            ->assertSee('Fresh Coat Care Guide', false);

        $this->actingAs($admin)
            ->get(route('admin.blog-posts.index', ['s' => 'Fresh Coat Care Guide']))
            ->assertOk()
            ->assertSee('Published')
            ->assertSee('Drafts')
            ->assertSee('Fresh Coat Care Guide');

        $this->actingAs($admin)
            ->get(route('admin.blog-posts.edit', BlogPost::query()->where('slug', 'fresh-coat-care-guide')->firstOrFail()))
            ->assertOk()
            ->assertSee('Publish', false)
            ->assertSee('Categories', false)
            ->assertSee('Featured image', false);
    }
}
