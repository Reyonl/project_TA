<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Template;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::with('admin')->get();
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required',
            'file_template' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'kategori' => 'required'
        ]);

        $path = $request->file('file_template')->store('templates', 'public');

        Template::create([
            'id_admin' => auth()->guard('admin')->id(),
            'nama_template' => $request->nama_template,
            'file_template' => $path,
            'kategori' => $request->kategori
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil diunggah.');
    }

    public function destroy(Template $template)
    {
        Storage::disk('public')->delete($template->file_template);
        $template->delete();
        return redirect()->route('admin.templates.index')->with('success', 'Template dihapus.');
    }
}
