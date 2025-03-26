<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Socialmedia;
use App\Traits\apiresponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class SocialmediaController extends Controller
{
    use apiresponse;

    public function addSocialMedia(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'platform' => 'nullable|array|max:4',
            'platform.*' => 'nullable|string',
            'url' => 'nullable|array|min:1|max:4',
            'url.*' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $user = auth()->user();

            
            $socialMediaData = [];
            $socialMediaCount = count($request->url);
            $responseMessage = [];

            
            if ($socialMediaCount > 0) {
                
                for ($i = 0; $i < $socialMediaCount; $i++) {
                    
                    $platform = isset($request->platform[$i]) ? $request->platform[$i] : '';

                   
                    $existingSocialMedia = $user->socialMedia()->where('url', $request->url[$i])->first();

                    if ($existingSocialMedia) {
                       
                        $existingSocialMedia->update([
                            'platform' => $platform,
                            'url' => $request->url[$i],
                        ]);
                        $responseMessage[] = "Social media for URL {$request->url[$i]} has been updated successfully.";
                    } else {
                      
                        $socialMediaData[] = [
                            'platform' => $platform,
                            'url' => $request->url[$i],
                        ];
                        $responseMessage[] = "Social media for URL {$request->url[$i]} has been added successfully.";
                    }
                }

                if (!empty($socialMediaData)) {
                    $user->socialMedia()->createMany($socialMediaData);
                }
            }

            DB::commit();
            return $this->success([
                'messages' => $responseMessage,
            ], 'Social media information has been processed successfully.', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred while adding or updating social media.', $e->getMessage());
        }
    }

}
