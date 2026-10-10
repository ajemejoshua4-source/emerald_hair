<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable; use Illuminate\Notifications\Notifiable;
class User extends Authenticatable { use Notifiable; protected $fillable=['name','email','password','phone','is_admin']; protected $hidden=['password','remember_token']; protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed','is_admin'=>'boolean']; } public function orders(){return $this->hasMany(Order::class);} public function wishlist(){return $this->hasMany(Wishlist::class);} }
