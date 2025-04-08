<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hobby;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HobbyController extends Controller
{
    use apiresponse;
    public function get()
    {
        $data = Hobby::where('status', 'active')->orderBy('priority', 'ASC')->get();
        return $this->success($data->select('id', 'name'), 'Data Fetch Successfully!', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hobby' => 'required|array', // Ensure it's an array
            'hobby.*' => 'string', // Each hobby should be a string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $user->hobby = json_encode($validator->validated()['hobby']); // Access validated data here
        $user->save();

        return $this->success($user, 'Data Stored Successfully!', 200);
    }

}
