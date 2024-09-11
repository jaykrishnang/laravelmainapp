<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showCreateForm()
    {
        return view('create-post');
    }

    public function storeNewPost(Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $incomingFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingFields);

        return redirect("/post/{$newPost->id}")->with('success', 'New Post created');
    }

    public function viewSinglePost(Post $post)
    {
        // if($post->user_id == auth()->id())
        // {
        //     return 'You are the author';
        // }
        // return 'You are not the author';
        $post['body'] = strip_tags(Str::markdown($post['body']), '<p><ul><li><ol><em><strong><h3><break>');
        return view('single-post', ['post' => $post]);
    }

    public function delete(Post $post)
    {
        if(auth()->user()->cannot('delete', $post))
        {
            return 'You cannot do';
        }

        $post->delete();

        return redirect('/profile/'.auth()->user()->username)->with('success', 'Successfully deleted');
    }
}
