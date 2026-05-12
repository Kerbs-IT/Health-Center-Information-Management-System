<?php

namespace App\Http\Controllers;

use App\Models\CarouselImage;
use Illuminate\Http\Request;

class manageInterfaceController extends Controller
{
    public function manageInterface()
    {
        $carouselImages = CarouselImage::orderBy('order')->get();

        return view('manage_interface.manageInterface', [
            'isActive'       => true,
            'page'           => 'MANAGE INTERFACE',
            'carouselImages' => $carouselImages,
        ]);
    }

    // ── LOGO ─────────────────────────────────────────────────────

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'max:10240'],
        ]);

        $destination = public_path('images/hugoperez_logo.png');

        $file   = $request->file('logo');
        $mime   = $file->getMimeType();
        $source = $file->getRealPath();

        $gdImage = match (true) {
            str_contains($mime, 'jpeg') => imagecreatefromjpeg($source),
            str_contains($mime, 'png')  => imagecreatefrompng($source),
            str_contains($mime, 'gif')  => imagecreatefromgif($source),
            str_contains($mime, 'webp') => imagecreatefromwebp($source),
            default                     => null,
        };

        if (! $gdImage) {
            return response()->json(['message' => 'Unsupported image format.'], 422);
        }

        imagesavealpha($gdImage, true);
        imagealphablending($gdImage, false);
        imagepng($gdImage, $destination, 9);
        imagedestroy($gdImage);

        return response()->json([
            'message' => 'Logo updated successfully.',
            'url'     => asset('images/hugoperez_logo.png') . '?v=' . time(),
        ]);
    }

    public function removeLogo()
    {
        $destination = public_path('images/hugoperez_logo.png');
        $default     = public_path('images/hugoperez_logo_default.png');

        if (file_exists($default)) {
            copy($default, $destination);
        }

        return response()->json(['message' => 'Logo removed.']);
    }

    // ── CAROUSEL ─────────────────────────────────────────────────

    public function uploadCarouselImage(Request $request)
    {
        $uploadPath = public_path('images/carousel');
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file = $request->file('image');

        if (! $file) {
            return response()->json([
                'message'          => 'No file received by server.',
                'php_upload_error' => $_FILES['image']['error'] ?? 'No $_FILES entry',
            ], 422);
        }

        if (! $file->isValid()) {
            $phpErrors = [
                0 => 'No error',
                1 => 'File exceeds upload_max_filesize in php.ini',
                2 => 'File exceeds MAX_FILE_SIZE in HTML form',
                3 => 'File was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing temp folder',
                7 => 'Failed to write file to disk',
                8 => 'Upload stopped by PHP extension',
            ];

            return response()->json([
                'message'          => 'File upload failed at PHP level.',
                'php_error_code'   => $file->getError(),
                'php_error_reason' => $phpErrors[$file->getError()] ?? 'Unknown error',
            ], 422);
        }

        $validator = \Validator::make($request->all(), [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'   => 'Validation failed.',
                'errors'    => $validator->errors(),
                'file_info' => [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'size_kb'       => round($file->getSize() / 1024, 2),
                    'extension'     => $file->getClientOriginalExtension(),
                ],
            ], 422);
        }

        $filename = 'carousel_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        $lastOrder = CarouselImage::max('order') ?? 0;

        $slide = CarouselImage::create([
            'path'  => 'images/carousel/' . $filename,
            'order' => $lastOrder + 1,
        ]);

        return response()->json([
            'id'  => $slide->id,
            'url' => asset($slide->path),
        ]);
    }

    public function deleteCarouselImage($id)
    {
        $slide = CarouselImage::findOrFail($id);

        $fullPath = public_path($slide->path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $slide->delete();

        CarouselImage::orderBy('order')
            ->get()
            ->each(function ($img, $index) {
                $img->update(['order' => $index + 1]);
            });

        return response()->json(['message' => 'Slide removed.']);
    }

    public function reorderCarousel(Request $request)
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $index => $id) {
            CarouselImage::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Order saved.']);
    }
}
