<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\BlogPost;
use App\Models\Comment;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class BlogPageSingle extends Component
{
    public BlogPost $post;
    public $comments;
    public $newComment = [
        'title' => '',
        'content' => ''
    ];

    public function mount($post_slug)
    {
        $this->post = BlogPost::postSingle()
            ->byPostSlug($post_slug)
            ->visible()
            ->firstOrFail();
        
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = $this->post->comments()
            ->where('is_visible', true)
            ->with('user')
            ->latest()
            ->get();
    }

    public function addComment()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to comment.');
            return;
        }

        $this->validate([
            'newComment.content' => 'required|min:3|max:1000',
            'newComment.title' => 'nullable|max:255'
        ]);

        $comment = new Comment();
        $comment->title = $this->newComment['title'];
        $comment->content = $this->newComment['content'];
        $comment->is_visible = true; // Or set to false if you want admin approval
        $comment->user_id = Auth::id();
        
        $this->post->comments()->save($comment);

        // Reset form
        $this->newComment = ['title' => '', 'content' => ''];
        
        // Reload comments
        $this->loadComments();
        
        session()->flash('message', 'Comment added successfully!');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.blog-page-single', [
            'post' => $this->post,
        ]);
    }
}