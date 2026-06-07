<?php

namespace App\Http\Controllers;

use App\Models\BlogSetting;
use Illuminate\Http\Request;

class BlogSettingController extends Controller
{
    public function edit()
    {
        $settings = BlogSetting::first() ?? new BlogSetting();
        return view('blog-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'blog_name'    => ['required', 'string', 'max:255'],
            'bio'          => ['nullable', 'string'],
            'bg_color'     => ['required', 'string', 'max:7'],
            'text_color'   => ['required', 'string', 'max:7'],
            'accent_color' => ['required', 'string', 'max:7'],
            'font'         => ['required', 'string'],
            'avatar'       => ['nullable', 'image', 'max:2048'],
        ]);

        $settings = BlogSetting::first() ?? new BlogSetting();

        $data = $request->only('blog_name', 'bio', 'bg_color', 'text_color', 'accent_color', 'font');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $settings->fill($data)->save();

        return redirect('/blog-settings')->with('success', '¡Configuración guardada!');
    }
}
