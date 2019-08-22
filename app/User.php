<?php

namespace App;
//use App\Traits\Encryptable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Support\Facades\Crypt;
class User extends Authenticatable
{
	//use Encryptable;
    use Notifiable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'password',
        'phone',
        'address',
        'image',
        'social_security_number',
        'provider_type',
        'hourly_rate',
        'max_hours',
        'email_notification',
        'status',
    ];
	/* protected $encryptable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'address',
        'image',
        'social_security_number',
        'provider_type',
        'hourly_rate',
        'max_hours',
        'email_notification',
        'status',
    ];  */
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
	// public function setAttribute($key, $value)
  //   {
  //       if (in_array($key, $this->encryptable)) {
  //           $value = Crypt::encrypt($value);
  //       }
  //       return parent::setAttribute($key, $value);
  //   }
	// public function getAttribute($key)
  //   {
  //       $value = parent::getAttribute($key);
  //
  //       if (in_array($key, $this->encryptable)) {
  //           $value = Crypt::decrypt($value);
  //       }
	// 	return $value;
  //   }
	// public static function getuser(){
	// 	$user = User::get()->toArray();
  //
	// 	return $user;
	// }
}
