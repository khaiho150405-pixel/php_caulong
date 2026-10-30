<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\TinTucRequest;
use App\Models\SanPham;
use App\Models\TinTuc;
use App\Models\TranDau;
use App\Services\Ai\GeminiChatbotService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class QuanLyBangTinController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $dateRange = trim((string) $request->query('date_range', ''));
        $loai = array_key_exists((string) $request->query('loai'), TinTuc::LOAI) ? $request->query('loai') : null;

        $query = TinTuc::with(['sanPham', 'tranDau', 'tacGia'])
            ->loai($loai)
            ->timKiem($keyword);

        if ($dateRange !== '') {
            $parts = preg_split('/\s+(?:to|-)\s+/i', $dateRange);
            if (count($parts) === 2) {
                try {
                    $from = Carbon::parse(trim($parts[0]))->startOfDay();
                    $to = Carbon::parse(trim($parts[1]))->endOfDay();
                    $query->whereBetween('NgayDang', [$from, $to]);
                } catch (Exception $e) {

                }
            }
        }

        $list = $query->orderBy('ThuTu')
            ->orderByDesc('NgayDang')
            ->orderByDesc('MaTin')
            ->paginate(10)
            ->withQueryString();

        return view('QuanLyBangTin.Index', compact('list', 'keyword', 'loai', 'dateRange'));
    }

    public function create()
    {
        return view('QuanLyBangTin.Create', $this->duLieuForm());
    }

    public function createPost(TinTucRequest $request)
    {
        $data = $request->validated();
        $data['NgayDang'] = $data['NgayDang'] ?? now();
        $data['MaTaiKhoan'] = Auth::id();

        if ($request->hasFile('HinhAnh_file')) {
            $file = $request->file('HinhAnh_file');
            $uploadDir = public_path('images/uploads/tintuc');
            File::ensureDirectoryExists($uploadDir);

            $filename = 'news_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['HinhAnh'] = 'images/uploads/tintuc/'.$filename;
        }
        unset($data['HinhAnh_file']);

        $tin = TinTuc::create($data);

        if ($request->input('action') === 'stay' || $request->has('save_stay')) {
            return redirect()->route('quanlybantin.edit', $tin->MaTin)
                ->with('message', 'Lưu bài viết thành công!');
        }

        return redirect()->route('quanlybantin.index')->with('message', 'Đăng bài lên bảng tin thành công!');
    }

    public function edit($id)
    {
        $tin = TinTuc::findOrFail($id);

        return view('QuanLyBangTin.Edit', ['tin' => $tin] + $this->duLieuForm());
    }

    public function editPost(TinTucRequest $request, $id)
    {
        $tin = TinTuc::findOrFail($id);
        $data = $request->validated();
        $data['NgayDang'] = $data['NgayDang'] ?? $tin->NgayDang;

        if ($request->hasFile('HinhAnh_file')) {
            $file = $request->file('HinhAnh_file');
            $uploadDir = public_path('images/uploads/tintuc');
            File::ensureDirectoryExists($uploadDir);

            $filename = 'news_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['HinhAnh'] = 'images/uploads/tintuc/'.$filename;
        }
        unset($data['HinhAnh_file']);

        $tin->update($data);

        if ($request->input('action') === 'stay' || $request->has('save_stay')) {
            return redirect()->route('quanlybantin.edit', $tin->MaTin)
                ->with('message', 'Cập nhật bài viết thành công!');
        }

        return redirect()->route('quanlybantin.index')->with('message', 'Cập nhật bài viết thành công!');
    }

    public function delete($id)
    {
        $tin = TinTuc::with(['sanPham', 'tranDau'])->findOrFail($id);

        return view('QuanLyBangTin.Delete', compact('tin'));
    }

    public function deleteConfirmed($id)
    {
        TinTuc::findOrFail($id)->delete();

        return redirect()->route('quanlybantin.index')->with('message', 'Đã xóa bài viết khỏi bảng tin.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $tin = TinTuc::findOrFail($id);
        $field = $request->input('field');

        if (! in_array($field, ['NoiBat', 'HienThi'], true)) {
            return response()->json(['success' => false, 'message' => 'Trường cập nhật không hợp lệ.'], 400);
        }

        $value = $request->has('value') ? $request->boolean('value') : ! (bool) $tin->{$field};
        $tin->{$field} = $value;
        $tin->save();

        return response()->json([
            'success' => true,
            'field' => $field,
            'value' => (bool) $value,
            'message' => $field === 'NoiBat'
                ? ($value ? 'Đã gạt nổi bật: bài viết sẽ được hiển thị ở vị trí chính!' : 'Đã tắt nổi bật bài viết.')
                : ($value ? 'Đã bật hiển thị: bài viết đã xuất hiện trên website!' : 'Đã tắt hiển thị: bài viết đã bị ẩn khỏi website!'),
        ]);
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('upload') ?? $request->file('upload_image');

        if (! $file || ! $file->isValid()) {
            return response()->json([
                'uploaded' => 0,
                'error' => ['message' => 'Không tìm thấy file hình ảnh tải lên hoặc file không hợp lệ.'],
            ], 400);
        }

        $uploadDir = public_path('images/uploads/editor');
        File::ensureDirectoryExists($uploadDir);

        $filename = 'editor_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);

        $url = asset('images/uploads/editor/'.$filename);

        $funcNum = $request->input('CKEditorFuncNum');
        if ($funcNum) {
            $msg = 'Tải ảnh lên thành công!';

            return response("<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '{$url}', '{$msg}');</script>")
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return response()->json([
            'uploaded' => 1,
            'fileName' => $filename,
            'url' => $url,
        ]);
    }

    public function aiGenerate(Request $request, GeminiChatbotService $gemini)
    {
        $topic = (string) $request->input('topic', $request->input('title', ''));
        $summary = $request->input('summary');

        $result = $gemini->generateArticle($topic, $summary);

        return response()->json($result);
    }

    private function duLieuForm(): array
    {
        return [
            'sanPhams' => SanPham::orderBy('TenSanPham')->get(['MaSanPham', 'TenSanPham']),
            'tranDaus' => TranDau::orderByDesc('NgayThiDau')->orderByDesc('MaTranDau')->take(200)->get(),
        ];
    }
}
