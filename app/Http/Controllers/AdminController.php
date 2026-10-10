<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
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

    public function products(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $products = Product::with('category')
            ->withCount('variants')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', '%'.$q.'%')
                        ->orWhere('slug', 'like', '%'.$q.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'q'));
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
        $product->update($this->validatedProduct($request));

        return back()->with('success', 'Product updated.');
    }

    public function productDelete(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    /*
    |--------------------------------------------------------------------------
    | Product variants
    |--------------------------------------------------------------------------
    */

    public function variants(Product $product)
    {
        return view('admin.products.variants', [
            'product' => $product,
            'variants' => $product->variants()->orderBy('label')->get(),
        ]);
    }

    public function variantStore(Request $request, Product $product)
    {
        $product->variants()->create($this->validatedVariant($request));

        return back()->with('success', 'Variant added.');
    }

    public function variantUpdate(Request $request, ProductVariant $variant)
    {
        $variant->update($this->validatedVariant($request, $variant));

        return back()->with('success', 'Variant updated.');
    }

    public function variantDelete(ProductVariant $variant)
    {
        $variant->delete();

        return back()->with('success', 'Variant removed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Orders & enquiries
    |--------------------------------------------------------------------------
    */

    public function orders(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $orders = Order::with('items')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('order_number', 'like', '%'.$q.'%')
                        ->orWhere('customer_name', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'q'));
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

    /*
    |--------------------------------------------------------------------------
    | Delivery zones
    |--------------------------------------------------------------------------
    */

    public function deliveryZones()
    {
        return view('admin.delivery-zones', [
            'zones' => DeliveryZone::orderBy('name')->get(),
        ]);
    }

    public function deliveryZoneStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'fee' => ['required', 'numeric', 'min:0'],
        ]);

        DeliveryZone::create([
            'name' => $data['name'],
            'fee' => $data['fee'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Delivery zone added.');
    }

    public function deliveryZoneDelete(DeliveryZone $zone)
    {
        $zone->delete();

        return back()->with('success', 'Delivery zone removed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Store settings
    |--------------------------------------------------------------------------
    */

    public function settings()
    {
        return view('admin.settings');
    }

    public function settingsUpdate(Request $request)
    {
        $data = $request->validate([
            'store_email' => ['nullable', 'email', 'max:150'],
            'store_phone' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'announcement' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }

    /*
    |--------------------------------------------------------------------------
    | Validation helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    private function validatedProduct(Request $request): array
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

    /**
     * @return array<string, mixed>
     */
    private function validatedVariant(Request $request, ?ProductVariant $variant = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'sku' => ['nullable', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
