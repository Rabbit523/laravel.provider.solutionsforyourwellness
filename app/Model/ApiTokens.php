<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ApiTokens extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'api_tokens';

	protected $fillable = ['device_id', 'user_id', 'auth_token', 'created_at', 'updated_at'];
}
