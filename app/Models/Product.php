<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class Product extends Model { protected $fillable=['category_id','name','slug','description','price','price_on_request','stock','image','badge','is_active','featured']; protected function casts():array{return ['price'=>'decimal:2','price_on_request'=>'boolean','is_active'=>'boolean','featured'=>'boolean'];} public function category(){return $this->belongsTo(Category::class);} public function wishlist(){return $this->hasMany(Wishlist::class);} public function orderItems(){return $this->hasMany(OrderItem::class);} }
