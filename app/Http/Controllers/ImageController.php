<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    public function upload(ImageUploadRequest $request): JsonResponse
    {
        $path = $request->file('image')->store('images', 'public');
        $url  = Storage::disk('public')->url($path);

        return response()->json([
            'filename' => basename($path),
            'url'      => $url,
        ], Response::HTTP_CREATED);
    }
}
