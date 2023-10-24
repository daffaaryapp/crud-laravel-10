<?php

namespace App\Http\Controllers;

//import Model "Post
use App\Models\Post;
use Illuminate\View\View;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

//import Facade "Storage"
use Illuminate\Support\Facades\Storage;

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


    public function edit(string $id): View
    {
        $post = Post::findOrfail($id);
        //render view with post
        return view('posts.edit', compact('post'));

    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'image'     => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);
	
        //get post by ID
        $post = Post::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload gambar baru
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
                        
            //hapus gambar lama
            Storage::delete('public/posts/'.$post->image);

            //update data ke database
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);
            
        } else {

            //update data ke database
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        }
	
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }
}
