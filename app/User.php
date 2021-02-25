<?php

namespace Servidor;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification as Notification;
use Illuminate\Notifications\DatabaseNotificationCollection as Notifications;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;

/**
 * A User is a Servidor *backend* user; not to be confused with SystemUsers!
 *
 * @property int                          $id
 * @property string                       $name
 * @property string                       $email
 * @property ?string                      $email_verified_at
 * @property string                       $password
 * @property ?string                      $remember_token
 * @property ?Carbon                      $created_at
 * @property ?Carbon                      $updated_at
 * @property Collection|Client[]          $clients
 * @property ?int                         $clients_count
 * @property Notifications|Notification[] $notifications
 * @property ?int                         $notifications_count
 * @property Collection|Token[]           $tokens
 * @property ?int                         $tokens_count
 *
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
