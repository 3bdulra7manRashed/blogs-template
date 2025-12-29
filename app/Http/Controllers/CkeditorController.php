<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CkeditorController extends Controller
{
    /**
     * Handle CKEditor image upload
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'upload' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120' // Max 5MB
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first('upload')
            ], 422);
        }

        try {
            $file = $request->file('upload');
            
            // Store file in storage/app/public/ckeditor
            $path = $file->store('ckeditor', 'public');
            
            // Generate public URL
            $url = Storage::url($path);
            
            // Return JSON response expected by CKEditor
            return response()->json([
                'url' => $url,
                'uploaded' => 1,
                'fileName' => $file->getClientOriginalName()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل رفع الصورة. حاول مرة أخرى.'
            ], 500);
        }
    }
}

