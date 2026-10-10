<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'products' => Product::count(),
            'orders' => Order::count(),
            'enquiries' => Enquiry::where('status', 'new')->count(),
            'revenue' => (float) Order::where('payment_status', 'paid')->sum('total'),
            'lowStock' => Product::where('stock', '<=', 5)->orderBy('stock')->take(5)->get(),
        ]);
    }

    public function products()
    {
        return view('admin.products.index', [
            'products' => Product::with('category')->latest()->paginate(20),
        ]);
    }

    public function productCreate()
    {
        return view('admin.products.form', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function productStore(Request $request)
    {
        $data = $this->validatedProduct($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));

        Product::create($data);

        return redirect()->route('admin.products')->with('success', 'Product created.');
    }

    public function productEdit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function productUpdate(Request $request, Product $product)
    {
        $product->update($this->validatedProduct($request, $product));

        return back()->with('success', 'Product updated.');
    }

    public function productDelete(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    public function categories()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('products')->latest()->get(),
        ]);
    }

    public function categoryStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Category created.');
    }

    public function categoryDelete(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function orders()
    {
        return view('admin.orders.index', [
            'orders' => Order::with('items')->latest()->paginate(20),
        ]);
    }

    public function orderUpdate(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,completed,cancelled'],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
        ]);

        $order->update($data);

        return back()->with('success', 'Order updated.');
    }

    public function enquiries()
    {
        return view('admin.enquiries.index', [
            'enquiries' => Enquiry::latest()->paginate(20),
        ]);
    }

    public function enquiryUpdate(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'status' => ['required', 'in:new,read,resolved'],
        ]);

        $enquiry->update(['status' => $request->string('status')->toString()]);

        return back()->with('success', 'Enquiry updated.');
    }

    /**
     * Validate the incoming product payload and normalise booleans/uploads.
     *
     * @return array<string, mixed>
     */
    private function validatedProduct(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_on_request' => ['boolean'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'badge' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'featured' => ['boolean'],
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $name = Str::slug($data['name']).'-'.Str::lower(Str::random(6)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $name);
            $data['image'] = 'uploads/products/'.$name;
        }

        unset($data['image_file']);

        $data['price_on_request'] = $request->boolean('price_on_request');
        $data['is_active'] = $request->boolean('is_active');
        $data['featured'] = $request->boolean('featured');

        if ($data['price_on_request']) {
            $data['price'] = null;
        }

        return $data;
    }
}
