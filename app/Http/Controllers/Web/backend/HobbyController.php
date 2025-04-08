<?php

namespace App\Http\Controllers\Web\backend;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Hobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class HobbyController extends Controller
{

    protected $hobby;
    public function __construct(Hobby $hobby)
    {
        $this->hobby = $hobby;
    }
    public function get(Request $request)
    {
        $query = $this->hobby::query();

        if (!empty($request->id) && empty($request->name) && empty($request->priority)) {
            $query->where('id', $request->id);
        }
        if (!empty($request->name)) {
            if (!empty($request->id)) {
                $query->where('id', '!=', $request->id)->where('name', $request->name);
            }
            $query->where('name', $request->name);
        }
        if (!empty($request->priority)) {
            if (!empty($request->id)) {
                $query->where('id', '!=', $request->id)->where('priority', $request->priority);
            }
            $query->where('priority', $request->priority);
        }

        $brands = $query->get();

        return response()->json($brands);
    }

    public function priority(Request $request)
    {
        try {
            foreach ($request->ranks as $rankData) {
                $Brand = $this->hobby::find($rankData['id']);
                if ($Brand) {
                    $Brand->priority = $rankData['rank'];
                    $Brand->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Priority updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update priority', 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request)
    {
        $cate = $this->hobby::find($request->id);


        if ($cate->status == 'active') {
            $cate->update([
                'status' => 'inactive',
            ]);
        } else {
            $cate->update([
                'status' => 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status Updated'
        ]);
    }

    public function destroy($id)
    {
        $delete = $this->hobby::find($id)->update([
            'priority' => 0
        ]);

        $delete = $this->hobby::find($id)->delete();
        if ($delete) {
            return back()->with('success', 'Deleted Successfully');
        } else {
            return back()->with('error', 'Try Again!');
        }
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->hobby::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="statusBrand(' . $data->id . ')" type="checkbox" ' . ($data->status == 'active' ? 'checked' : '') . '>
                            </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<button onclick="editBrand(' . $data->id . ')" type="button" class="btn btn-info">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button"  onclick="deleteData(\'' . route('hobby.destroy', $data->id) . '\')" class="btn btn-danger del">
                                <i class="mdi mdi-delete"></i>
                            </button>';
                })
                ->setRowAttr([
                    'data-id' => function ($data) {
                        return $data->id;
                    }
                ])
                ->rawColumns(['status', 'action'])
                ->make(true);
        }


        return view('backend.layout.hobby.index');
    }

    public function store(Request $request)
    {
        
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:hobbies,name',
            'priority' => 'required|numeric|unique:hobbies,priority',
        ], [
            'name.required' => 'The Brand name is required.',
            'name.string' => 'The Brand name must be a valid string.',
            'name.max' => 'The Brand name cannot exceed 255 characters.',
            'name.unique' => 'This Brand name is already taken. Please choose a different name.',

            'priority.numeric' => 'The priority must be a number.',
            'priority.unique' => 'This priority value is already taken. Please choose a different one.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput();
        }
       
        try {
            // Auto-generate priority if not provided
            if (empty($data['priority'])) {
                $highestPriority = $this->hobby::max('priority');
                $data['priority'] = $highestPriority ? $highestPriority + 1 : 1;
            }
            $data['slug'] = Str::slug($request->name);
            $Brand = $this->hobby::create($data);

            return back()->with('success', 'Brand successfully created');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hobbies', 'name')->ignore($request->id),
            ],
            'priority' => [
                'required',
                'numeric',
                Rule::unique('hobbies', 'priority')->ignore($request->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'The Brand name is required.',
            'name.string' => 'The Brand name must be a valid string.',
            'name.max' => 'The Brand name cannot exceed 255 characters.',
            'name.unique' => 'This Brand name is already taken. Please choose a different name.',

            'priority.required' => 'The priority field is required.',
            'priority.numeric' => 'The priority must be a number.',
            'priority.unique' => 'This priority value is already taken. Please choose a different one.',

            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Only JPEG, PNG, JPG, and GIF formats are allowed.',
            'image.max' => 'The image size must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            // return response()->json(['errors' => $validator->errors()], 422);
            return back()->with('error', $validator->errors()->first())->withInput();
        }
        $Brand = $this->hobby::find($request->id);
        try {
            if ($request->hasFile('image')) {
                if (file_exists($Brand->image) && $Brand->image != 'default.jpg') {
                    unlink($Brand->image);
                }
                $data['image'] = Helper::fileUpload($request->image, 'brands', $request->name . "-" . time());
            }
            $data['slug'] = $Brand->name != $request->name ? Str::slug($request->name) : $Brand->slug;
            $Brand = $Brand->update($data);
            // return response()->json(['message' => 'Brand created successfully!', 'service' => $service], 201);
            return back()->with('success', 'Brand successfully created');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
