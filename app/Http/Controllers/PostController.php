<?php

namespace App\Http\Controllers;

//import Model "Post
use App\Models\Post;
use Illuminate\View\View;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): View
    {
        //get posts
        $posts = Post::latest()->paginate(5);
        //render view with posts
        return view('posts.index', compact('posts'));
	
    }

    public function create(): View
    {
        return view('posts.create');
    }
    
    public function store(Request $request): RedirectResponse
    {
        //validate form
        $this->validate($request, [
        'image'     => 'required|image|mimes:jpeg,jpg,png|max:2048',
        'title'     => 'required|min:5',
        'content'   => 'required|min:10'
        ]);
        
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
}
