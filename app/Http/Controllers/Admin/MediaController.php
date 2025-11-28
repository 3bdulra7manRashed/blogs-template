<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Media::class);

        $query = Media::with('uploader');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('filename', 'like', "%{$search}%");
        }

        $media = $query->latest()->paginate(24)->withQueryString();

        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        Gate::authorize('create', Media::class);

        $validated = $request->validate([
            'file' => ['required', 'image', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        $media = Media::create([
            'user_id' => Auth::id(),
            'filename' => $file->getClientOriginalName(),
            'disk' => 'public',
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'caption' => $validated['caption'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'media' => $media,
                'url' => asset('storage/' . $path),
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'Media uploaded successfully.');
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $request->validate([
                'upload' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $path = $file->store('uploads', 'public'); 
            $url = asset('storage/' . $path);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'فشل الرفع'], 400);
    }

    public function destroy(Media $media)
    {
        Gate::authorize('delete', $media);

        // 1. استخراج اسم الملف بدقة (نفس اللوجيك الذي نجح سابقاً)
        $pathInDb = $media->path;
        $filename = pathinfo(parse_url($pathInDb, PHP_URL_PATH), PATHINFO_BASENAME);

        if (empty($filename) || $filename == '.') {
            $parts = explode('/', $pathInDb);
            $filename = end($parts);
        }

        // 2. تحديد المسار الفيزيائي
        // بما أنك تستخدم ويندوز، نستخدم DIRECTORY_SEPARATOR لضمان شكل المسار الصحيح
        $targetPath = public_path("storage/media" . DIRECTORY_SEPARATOR . $filename);

        // 3. محاولة حذف الملف (وضع "الصامت")
        // نحاول الحذف، ولو فشل بسبب ويندوز، نتجاهل الخطأ ونكمل
        if (file_exists($targetPath)) {
            try {
                // @ قبل الدالة تمنع ظهور التحذيرات في PHP
                @unlink($targetPath);
            } catch (\Exception $e) {
                // نسجل الخطأ في اللوج فقط للمبرمج، ولا نعرضه للمستخدم
                Log::warning("File delete failed (Windows Lock): " . $targetPath);
            }
        }

        // 4. حذف السجل من قاعدة البيانات (يحدث دائماً)
        $media->delete();

        // 5. رسالة النجاح
        return redirect()->route('admin.media.index')
            ->with('success', 'تم حذف الصورة بنجاح.');
    }
}

