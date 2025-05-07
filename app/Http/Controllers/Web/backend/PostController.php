<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Post::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bulk_check', function ($data) {
                    return '<div class="form-checkbox">
                                <input type="checkbox" class="form-check-input select_data"
                                       id="checkbox-' . $data->id . '"
                                       value="' . $data->id . '"
                                       onClick="select_single_item(' . $data->id . ')">
                                <label class="form-check-label" for="checkbox-' . $data->id . '"></label>
                            </div>';
                })
                ->editColumn('image', function ($data) {
                    $imagesHtml = '';

                    if ($data->images && $data->images->isNotEmpty()) {
                        $imagesHtml .= '<div style="display: flex; flex-wrap: wrap; gap: 5px;">';

                        foreach ($data->images as $image) {
                            $url = $image->file_url;
                            $imagesHtml .= '<img src="' . $url . '" alt="Image" width="80" height="80" style="object-fit: cover;">';
                        }

                        $imagesHtml .= '</div>';
                    }

                    return $imagesHtml ?: 'No images';
                })
                ->editColumn('status', function ($data) {
                    return '<div class="form-check form-switch mb-2"><input type="checkbox" class="form-check-input"
                            onclick="changeStatus(event,' . $data->id . ')"
                            ' . ($data->status == "active" ? "checked" : "") . '></div>';
                })
                ->addColumn('action', function ($data) {
                    return '<a href="' . route('dynamicpages.edit', $data->id) . '" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" onclick="showDeleteAlert(' . $data->id . ')" class="btn btn-sm btn-danger">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>';
                })
                ->rawColumns(['bulk_check', 'image', 'status', 'action'])
                ->make(true);
        }
        return view('backend.layout.post.index');
    }
}
