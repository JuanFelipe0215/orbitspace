<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only('title', 'content', 'is_published');
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        Post::create($data);

        return redirect()->route('posts.index')->with('success', 'Post creado exitosamente.');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only('title', 'content');
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $post->update($data);

        return redirect()->route('posts.index')->with('success', 'Post actualizado.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post eliminado.');
    }
}
