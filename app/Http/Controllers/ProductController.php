<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])->latest()->get();
        return response()->json($products);
    }

    public function paginate(Request $request)
    {
        $query = Product::with(['category', 'images'])->latest();

        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        return response()->json($query->paginate(8));
    }

  public function store(Request $request)
    {
        // 🚨 WRAPPED IN TRY-CATCH TO PREVENT FATAL CRASHES 🚨
        set_time_limit(300);
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:20480', 
                'sizes' => 'nullable|array',
                'sizes.*' => 'string|max:10',
                'additional_images' => 'nullable|array|max:5',
                'additional_images.*' => 'nullable|file|image|mimes:jpg,jpeg,png|max:20480',
            ]);

            $primaryImagePath = null;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $uploadedFileUrl = Storage::disk('cloudinary')->putFile('caps', $request->file('image'));
                $primaryImagePath = Storage::disk('cloudinary')->url($uploadedFileUrl);
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'sizes' => $request->sizes, 
                'image' => $primaryImagePath,
            ]);

            if ($request->hasFile('additional_images')) {
                $files = $request->file('additional_images');
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $imageFile) {
                    if ($imageFile->isValid()) {
                        $uploadedUrl = Storage::disk('cloudinary')->putFile('caps', $imageFile);
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => Storage::disk('cloudinary')->url($uploadedUrl)
                        ]);
                    }
                }
            }

            return response()->json($product->load('images'), 201);

        } catch (\Exception $e) {
            // IF ANYTHING FAILS, WE CATCH IT HERE AND SEND IT TO REACT
            return response()->json([
                'errors' => [
                    'server_crash' => [$e->getMessage()],
                    'file' => [$e->getFile()],
                    'line' => [$e->getLine()]
                ]
            ], 422); 
            // Using 422 so Axios handles it safely and CORS headers survive
        }
    }
    public function show(string $id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:20480',
            
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:10',
            
            'additional_images' => 'nullable|array|max:5',
            'additional_images.*' => 'nullable|file|image|mimes:jpg,jpeg,png|max:20480',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $uploadedFileUrl = Storage::disk('cloudinary')->putFile('caps', $request->file('image'));
            $validated['image'] = Storage::disk('cloudinary')->url($uploadedFileUrl);
        }
        
        $product->update($validated);

        if ($request->hasFile('additional_images')) {
            $files = $request->file('additional_images');
            
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $imageFile) {
                if ($imageFile->isValid()) {
                    $uploadedUrl = Storage::disk('cloudinary')->putFile('caps', $imageFile);
                    
                    $product->images()->create([
                        'image_path' => Storage::disk('cloudinary')->url($uploadedUrl)
                    ]);
                }
            }
        }

        return response()->json($product->load('images'));
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return response()->json(['message' => 'Product deleted successfully']);
    }
}