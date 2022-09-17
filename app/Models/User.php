<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'hp',
        'gender',
        'jne_id',
        'jne_id_cod',
        'alamat',
        'kecamatan',
        'kota',
        'provinsi',
        'kodepos',
        'from',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $url = env('PASSWORD_RESET_URL') . '?token=' . $token;
        $this->notify(new ResetPasswordNotification($url));
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wa_sessions()
    {
        return $this->hasMany(WaSession::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function managers()
    {
        return $this->hasMany(Employee::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}
