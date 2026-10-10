<?php
namespace App\Http\Controllers;
use App\Models\{Product,Wishlist}; use Illuminate\Http\Request;
class WishlistController extends Controller { public function index(){return view('account.wishlist',['items'=>auth()->user()->wishlist()->with('product')->latest()->get()]);} public function toggle(Product $product){$w=Wishlist::where('user_id',auth()->id())->where('product_id',$product->id)->first();$w?$w->delete():Wishlist::create(['user_id'=>auth()->id(),'product_id'=>$product->id]);return back()->with('success',$w?'Removed from wishlist.':'Saved to wishlist.');} }
