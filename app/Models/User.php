<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'servicio_id', // Asegúrate de incluir esto
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }

    protected $appends = [
        'profile_photo_url',
    ];

    public function adminlte_image()
    {
        return url('images/logo2.jpg');
    }

    public function adminlte_desc()
    {
        return 'ADMINISTRADOR';
    }

    public function adminlte_profile_url()
    {
        return '';
    }
}
