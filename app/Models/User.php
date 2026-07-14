<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Facades\Filament;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar
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
        'grade_id',
        'weapon_branch_id',
        'catalog_number',
        'avatar_url',
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
            'password' => 'hashed',
        ];
    }

    public function mealAttendances()
    {
        return $this->hasMany(MealAttendance::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function weaponBranch()
    {
        return $this->belongsTo(WeaponBranch::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = Filament::getPanel('admin')->getResetPasswordUrl($token, $this);

        $this->notify(
            new class ($url) extends ResetPassword {
            public function __construct(public string $url)
            {}

            public function toMail($notifiable): MailMessage
            {
                return (new MailMessage)
                    ->subject('Restablecer contraseña')
                    ->line('Recibimos una solicitud para restablecer tu contraseña.')
                    ->action('Restablecer contraseña', $this->url)
                    ->line('Si no solicitaste este cambio, puedes ignorar este correo.');
            }
            }
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function shiftAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ShiftAssignment::class);
    }
}
