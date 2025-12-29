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
            'file' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar',
                'max:5120', // 5MB
            ],
            'caption' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ], [
            'file.required' => 'يجب اختيار ملف للرفع.',
            'file.file' => 'الملف المرفوع غير صالح.',
            'file.mimes' => 'نوع الملف غير مدعوم. الأنواع المدعومة: jpeg, jpg, png, gif, webp, pdf, doc, docx, xls, xlsx, ppt, pptx, zip, rar',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت.',
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
            ->with('success', 'تم رفع الملف بنجاح.');
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

    // لاحظ أننا غيرنا المستقبل من (Media $media) إلى ($id)
public function destroy($id)
{
    // 1. البحث اليدوي عن الملف (شامل المحذوفات مؤقتاً لضمان العثور عليه)
    $media = Media::withTrashed()->find($id);

    // إذا لم يتم العثور على الملف، نرسل خطأ
    if (!$media) {
        return redirect()->route('admin.media.index')
            ->with('error', 'الصورة غير موجودة أو تم حذفها بالفعل.');
    }

    Gate::authorize('delete', $media);

    // 2. حذف الملف الفيزيائي (كما في كودك السابق)
    if ($media->path && Storage::disk('public')->exists($media->path)) {
        try {
            Storage::disk('public')->delete($media->path);
        } catch (\Exception $e) {
            Log::warning("فشل حذف الملف الفيزيائي: " . $e->getMessage());
        }
    }

    // 3. الحذف النهائي من الداتا بيز
    // نستخدم forceDelete لأنك جلبت الموديل، وهي تعمل سواء كان هناك SoftDeletes أم لا
    $media->forceDelete();

    return redirect()->route('admin.media.index')
        ->with('success', 'تم حذف الصورة نهائياً.');
}
}

