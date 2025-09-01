<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_number',
        'address_1',
        'address_2',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'bio'               => 'array',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'user_id', 'id');
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'user_id', 'id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'user_id', 'id');
    }


    public function canAccessPanel(Panel $panel): bool
    {
        $user    = Auth::user();
        $panelId = $panel->getId();

        return match ($panelId) {
            'admin' => $user && $user->hasRole('super_admin'),
            'service'     => $user && $user->hasAnyRole(['mechanic']),
            'customer'    => $user && $user->hasAnyRole(['customer']),
            default       => true,
        };
    }

    public function usersPanel(): string
    {
        $role = $this->getRoleNames()->first();

        return match ($role) {
            'super_admin' => Filament::getPanel('admin')->getUrl(),
            'mechanic'    => Filament::getPanel('service')->getUrl(),
            'customer'    => route('page.customer-dashboard'),
            default       => route('filament.auth.auth.login'),
        };
    }
}
