<?php

namespace App\Http\Controllers;

use App\Models\CauHinh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class QuanLyLogoController extends Controller
{
    public function index()
    {
        $siteName = CauHinh::get('site_name', 'Shop Cầu Lông');
        $logoUrl = CauHinh::getLogoUrl();

        return view('QuanLyLogo.Index', compact('siteName', 'logoUrl'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:200'],
            'logo_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ], [
            'logo_file.mimes' => 'Hình ảnh logo phải có định dạng jpg, jpeg, png, gif hoặc webp.',
            'logo_file.max' => 'Kích thước file ảnh logo tối đa là 10MB.',
        ]);

        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $uploadDir = public_path('images'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'logo');
            File::ensureDirectoryExists($uploadDir);

            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $filename = 'logo_'.time().'_'.Str::random(6).'.'.$ext;
            $file->move($uploadDir, $filename);

            $storedPath = 'images/uploads/logo/'.$filename;
            CauHinh::set('logo_url', $storedPath, 'Logo chính thức của hệ thống');
            Cache::forget('cau_hinh_logo_url');
        }

        if (array_key_exists('site_name', $validated)) {
            CauHinh::set('site_name', (string) $validated['site_name'], 'Tên website');
            Cache::forget('cau_hinh_site_name');
        }

        return redirect()->route('admin.logo.index')
            ->with('message', 'Cập nhật logo hệ thống thành công!');
    }
}
