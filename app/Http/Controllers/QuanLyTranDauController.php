<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\TranDauRequest;
use App\Models\TranDau;
use Illuminate\Http\Request;

class QuanLyTranDauController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        $list = TranDau::withCount('tinTucs')
            ->timKiem($keyword)
            ->orderByDesc('NgayThiDau')
            ->orderByDesc('MaTranDau')
            ->paginate(10)
            ->withQueryString();

        return view('QuanLyTranDau.Index', compact('list', 'keyword'));
    }

    public function create()
    {
        return view('QuanLyTranDau.Create');
    }

    public function createPost(TranDauRequest $request)
    {
        TranDau::create($request->validated());

        return redirect()->route('quanlytrandau.index')->with('message', 'Thêm kết quả trận đấu thành công!');
    }

    public function edit($id)
    {
        return view('QuanLyTranDau.Edit', ['tran' => TranDau::findOrFail($id)]);
    }

    public function editPost(TranDauRequest $request, $id)
    {
        TranDau::findOrFail($id)->update($request->validated());

        return redirect()->route('quanlytrandau.index')->with('message', 'Cập nhật trận đấu thành công!');
    }

    public function delete($id)
    {
        $tran = TranDau::withCount('tinTucs')->findOrFail($id);

        return view('QuanLyTranDau.Delete', compact('tran'));
    }

    public function deleteConfirmed($id)
    {

        TranDau::findOrFail($id)->delete();

        return redirect()->route('quanlytrandau.index')->with('message', 'Đã xóa trận đấu.');
    }
}
