<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Model\Pages_model;
use App\Model\User_model;
use App\Model\TimeZoneModel;
use App\Model\AdminSettings;
use App\Model\EmailTemplate;
use App\Model\EmailLog;
use App\Model\ApiTokens;
use Faker\Factory as Faker;
use Auth,
    Blade,
    Config,
    Cache,
    Cookie,
    File,
    Input,
    Html,
    Mail,
    mongoDate,
    Redirect,
    Response,
    Session,
    URL,
    View,
    Validator,
    Toast;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
// use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Helpers;

class UserController extends BaseController {

    /**
     * function login user.
     *
     * @param null
     *
     * @return response with user data on success otherwise error.
     */
    public function login(Request $request) {
        // return json_encode($request->all());
        // return json_encode($request->request);
        // $encrypted = Crypt::encryptString('Hello world.');
        // return json_encode($request['request']);
        // $decrypted = Crypt::decryptString($request['request']);
        // return json_encode($decrypted);

        // $input_data = $this->GetDecryptedData($request->all());
        // return json_encode($input_data);
        $request_data = $request->get('request');
        $input_data = json_decode($request_data);
        return $input_data;

        $input_data = $request->all();
        
        $rules = array(
            'email' => 'required',
            'password' => 'required',
            'device_id'       => 'required',
            'platform_type' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            // return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
            return response()->json(array('status' => 'error', 'message' => $validator->errors()->all()));
        } else {
            $email = $input_data['email'];
            $password = $input_data['password'];
            $device_id = $input_data['device_id'];
            $platform_type = $input_data['platform_type'];

            $user = User_model::GetUserByEmail($email);

            if (!empty($user)) {
                if ($user['status'] == 1) {
                    $userdata = array(
                        'email' => $email,
                        'password' => $password
                    );
                    //check if the user is logged already in another device or not
                    if (Auth::attempt($userdata, true)) {
                        // Creates token
                        $faker = Faker::create();
                        $auth_token = $faker->uuid();
                        $this->CreateAPItoken($user['id'], $device_id, $auth_token);
                        $upload_path = public_path('uploads/users' . DIRECTORY_SEPARATOR);
                        $userdata = array(
                            'user_id' => $user['id'],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'title' => $user['title'],
                            'email' => $user['email'],
                            'phone' => $user['phone'],
                            'address' => $user['address'],
                            'social_security_number' => $user['social_security_number'],
                            'provider_type' => $user['provider_type'],
                            'hourly_rate' => $user['hourly_rate'],
                            'max_hours' => $user['max_hours'],
                            'image' => WEBSITE_UPLOADS_URL . 'users/' . $user['image'],
                            'status' => $user['status'],
                            'created_at' => $user['created_at'],
                            'updated_at' => $user['updated_at'],
                        );
                       // echo json_encode(array('status' => 'success', 'message' => 'Logged in', 'user' => $userdata));die;
                       // die;                       
                        // return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Logged in', 'user' => $userdata)));
                        return response()->json(array('status' => 'success', 'message' => 'Logged in.', 'user' => $userdata));
                    } else {
                        return response()->json(array('status' => 'error', 'message' => 'Email or password incorrect.'));
                        // return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Email or password incorrect.')));
                    }
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Account disabled contact administrator.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found')));
            }
        }
    }

    /**
     * function create api token into database.
     *
     * @param user id,device id,auth_token
     *
     * @return response.
     */
    public function CreateAPItoken($user_id, $device_id, $auth_token) {
        $apiToken = ApiTokens::where('user_id', '=', $user_id)->first();
        if ($apiToken) {
            // Updates device_id and auth_token for the user
            $apiToken->update([
                'device_id' => $device_id,
                'auth_token' => $auth_token
            ]);
        } else {
            // Creates the api token
            $apiToken = ApiTokens::create([
                        'device_id' => $device_id,
                        'user_id' => $user_id,
                        'auth_token' => $auth_token,
            ]);
        }
    }

    /**
     * function for delete api token when user logout.
     *
     * @param user id,device id,auth_token
     *
     * @return response.
     */
    public function DeleteAPItoken($user_id, $device_id, $auth_token) {
        $auth = ApiTokens::Where(['user_id' => $user_id, 'device_id' => $device_id, 'auth_token' => $auth_token])->delete();
        return $auth;
    }

    /**
     * function for delete api token when user logout.
     *
     * @param user id,device id,auth_token
     *
     * @return response.
     */
    public function DeleteAPI($user_id) {
        $auth = ApiTokens::Where(['user_id' => $user_id])->delete();
        return $auth;
    }

    /**
     * function login user.
     *
     * @param null
     *
     * @return response with user data on success otherwise error.
     */
    public function PasswordVerifySecurityPin() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'password' => 'required',
            //'device_id'         => 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $password = $input_data['password'];
            $user = User_model::find($user_id);
            if (!empty($user)) {
                if (!Hash::check($password, $user->password)) {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Your password is incorrect.')));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Your password has been matched.', 'data' => $user->social_security_number)));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User does not exist.')));
            }
        }
    }

    /**
     * function for get user profile.
     *
     * @param null
     *
     * @return user data.
     */
    public function Getprofile() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'user_id' => 'required',
            //'device_id'  		=> 'required',
            'platform_type' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            if (isset($input_data['user_id']) && $input_data['user_id'] != null) {
                $user_id = $input_data['user_id'];
                $user = User_model::GetUserById($user_id);
                if (!empty($user)) {
                    if ($user['reminder_clock_in'] == 'Default') {
                        $clock_in_value = 'Default';
                    } else if ($user['reminder_clock_in'] == 0.5) {
                        $clock_in_value = ($user['reminder_clock_in'] * 60) . ' Minutes';
                    } else if ($user['reminder_clock_in'] == 1 || $user['reminder_clock_in'] == 2) {
                        $reminder_clock_in = User_model::select('reminder_clock_in')->where('id', $user_id)->first();
                        $clock_in_value = $reminder_clock_in->reminder_clock_in . ' Hour';
                    } else {
                        $clock_in_value = '2';
                    }
                    if ($user['reminder_clock_out'] == 1) {
                        $clock_out_value = 'Clock out after default time';
                    } else if ($user['reminder_clock_out'] == 0) {
                        $clock_out_value = 'Clock out after clinic schedule time';
                    } else {
                        $clock_out_value = '2';
                    }
                    if ($user['disable_email_confirmation'] == 1) {
                        $disable_email_confirmation = 1;
                    } else if ($user['disable_email_confirmation'] == 0) {
                        $disable_email_confirmation = 0;
                    } else {
                        $disable_email_confirmation = '2';
                    }
                    if ($user['location_leave_amount'] != null) {
                        $location_leave_amount = $user['location_leave_amount'] . ' min';
                    } else {
                        $location_leave_amount = '2';
                    }
                    if ($user['prep_time'] == 1440) {
                        $prep_time = '1 Day';
                    } elseif ($user['prep_time'] == 720) {
                        $prep_time = '12 Hours';
                    } elseif ($user['prep_time'] == 360) {
                        $prep_time = '6 Hours';
                    } else {
                        $prep_time = $user['prep_time'];
                    }
                    if ($user['system_calender'] == 0) {
                        $system_calender = 'off';
                    } elseif ($user['system_calender'] == 1) {
                        $system_calender = 'default';
                    } elseif ($user['system_calender'] == 2) {
                        $system_calender = 'sync';
                    }
                    if ($user['timezone'] != null) {
                        $time_zone = $user['timezone'];
                    } else {
                        $time_zone = $this->GetAdminSettingsValue('timezone');
                    }
                    if ($user['notification_groupby'] == 1) {
                        $notification_groupBy = 'Right away';
                    } elseif ($user['notification_groupby'] == 2) {
                        $notification_groupBy = 'Every hour';
                    } elseif ($user['notification_groupby'] == 3) {
                        $notification_groupBy = '2 times a day';
                    } elseif ($user['notification_groupby'] == 4) {
                        $notification_groupBy = '1 time a day';
                    } else {
                        $notification_groupBy = 'Right away';
                    }
                    if ($user['time_format'] == 24) {
                        $time_format = '24 Hours';
                    } elseif ($user['time_format'] == 12) {
                        $time_format = '12 Hours';
                    } else {
                        $time_format = '12 Hours';
                    }
                    $userimg = WEBSITE_UPLOADS_URL . 'users/' . $user['image'];
                    if (isset($user['image']) && $user['image'] != '') {
                        $user_image = $userimg;
                    } else {
                        $user_image = WEBSITE_UPLOADS_URL . 'users/man.png';
                    }

                    $userdata = array(
                        'user_id' => $user['id'],
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'title' => $user['title'],
                        'email' => $user['email'],
                        'phone' => $user['phone'],
                        'address' => $user['address'],
                        'social_security_number' => $user['social_security_number'],
                        'designation' => $user['provider_type'],
                        'hourly_rate' => $user['hourly_rate'],
                        'max_hours' => $user['max_hours'],
                        'email_notification' => $user['email_notification'],
                        'push_notification' => $user['push_notification'],
                        'user_description' => isset($user['user_description']) ? $user['user_description'] : "",
                        'clock_in_value' => $clock_in_value,
                        'clock_out_value' => $clock_out_value,
                        'leave_location_value' => $location_leave_amount,
                        'disable_status' => $disable_email_confirmation,
                        'prep_time' => $prep_time,
                        'system_calender_status' => $user['system_calender_status'],
                        'system_calender' => $system_calender,
                        'timezone' => $time_zone,
                        'time_format' => $time_format,
                        'notification_groupBy' => $notification_groupBy,
                        'image' => $user_image,
                        'created_at' => $user['created_at'],
                        'updated_at' => $user['updated_at'],
                    );

                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'ok', 'user' => $userdata)));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'No user found.')));
                }
            } else {

                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'user id is required.')));
            }
        }
    }

    /**
     * function for update user email address.
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateEmail() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'new_email' => 'required|email|unique:users,id',
            'old_email' => 'required|email',
            //'device_id'        	  	=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return array('status' => 'error', 'message' => $validator->errors()->all());
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($user['email'] == $input_data['old_email']) { // checking old email is match from database.
                    // calls update email function
                    $update = User_model::update_email($user_id, $input_data['new_email']);
                    if ($update) {
                        $user_data = User_model::GetUserById($user['id']);
                        $userdata = array(
                            'first_name' => $user_data['first_name'],
                            'last_name' => $user_data['last_name'],
                            'title' => $user_data['title'],
                            'email' => $user_data['email'],
                            'phone' => $user_data['phone'],
                            'address' => $user_data['address'],
                            'social_security_number' => $user_data['social_security_number'],
                            'provider_type' => $user_data['provider_type'],
                            'hourly_rate' => $user_data['hourly_rate'],
                            'max_hours' => $user_data['max_hours'],
                            'image' => $user_data['image'],
                            'status' => $user_data['status'],
                            'created_at' => $user_data['created_at'],
                            'updated_at' => $user_data['updated_at'],
                        );
                        return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Email updated.', 'user' => $userdata)));
                    } else {
                        return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                    }
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Old email is incorrect.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update user phone number.
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdatePhone() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'phone' => 'required',
            //'device_id'       => 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return array('status' => 'error', 'message' => $validator->errors()->all());
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                // calls update phone number function for update
                $update = User_model::update_phone($user_id, $input_data['phone']);
                if ($update) {
                    $user_data = User_model::GetUserById($user['id']);
                    $userdata = array(
                        'first_name' => $user_data['first_name'],
                        'last_name' => $user_data['last_name'],
                        'title' => $user_data['title'],
                        'email' => $user_data['email'],
                        'phone' => $user_data['phone'],
                        'address' => $user_data['address'],
                        'social_security_number' => $user_data['social_security_number'],
                        'provider_type' => $user_data['provider_type'],
                        'hourly_rate' => $user_data['hourly_rate'],
                        'max_hours' => $user_data['max_hours'],
                        'image' => $user_data['image'],
                        'status' => $user_data['status'],
                        'created_at' => $user_data['created_at'],
                        'updated_at' => $user_data['updated_at'],
                    );
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Phone number updated.', 'user' => $userdata)));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update user phone number.
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateSocialSecurity() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'social_security_number' => 'required',
            //'device_id'        	  		=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return array('status' => 'error', 'message' => $validator->errors()->all());
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                // calls update social security number function for update
                $update = User_model::update_social_security($user_id, $input_data['social_security_number']);
                if ($update) {
                    $user_data = User_model::GetUserById($user['id']);
                    $userdata = array(
                        'first_name' => $user_data['first_name'],
                        'last_name' => $user_data['last_name'],
                        'title' => $user_data['title'],
                        'email' => $user_data['email'],
                        'phone' => $user_data['phone'],
                        'address' => $user_data['address'],
                        'social_security_number' => $user_data['social_security_number'],
                        'provider_type' => $user_data['provider_type'],
                        'hourly_rate' => $user_data['hourly_rate'],
                        'max_hours' => $user_data['max_hours'],
                        'image' => $user_data['image'],
                        'status' => $user_data['status'],
                        'created_at' => $user_data['created_at'],
                        'updated_at' => $user_data['updated_at'],
                    );
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'S.S no. updated', 'user' => $userdata)));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update user profile.
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateProfile() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            // 'title'         		=> 'required',
            'address' => 'required',
            //'image'     			=> 'required',
            'provider_type' => 'required',
            // 'hourly_rate'     	=> 'required',
            // 'max_hours'     		=> 'required',
            //'device_id'        	=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return array('status' => 'error', 'message' => $validator->errors()->all());
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if (Input::file('image')) {
                    $image = Input::file('image');
                    // calls image upload function
                    $filename = $this->ImageUpload($image, $folder = 'users', $resize = true, ['50x50', '100x100', '200x200', '300x300', '400x400', '500x500']);
                } else {
                    // get image name from user data.
                    $filename = isset($user['image']) ? $user['image'] : '';
                }
                // calls update profile function for update
                $last_id = User_model::updateprofile($user_id, $input_data, $filename);
                if ($last_id) {
                    $user_data = User_model::GetUserById($user['id']);
                    $userdata = array(
                        'first_name' => $user_data['first_name'],
                        'last_name' => $user_data['last_name'],
                        'address' => $user_data['address'],
                        'provider_type' => $user_data['provider_type'],
                        'user_description' => $user_data['user_description'],
                        'image' => $user_data['image'],
                        'image_path' => WEBSITE_UPLOADS_URL . 'users/' . $user_data['image'],
                        'status' => $user_data['status'],
                        'created_at' => $user_data['created_at'],
                        'updated_at' => $user_data['updated_at'],
                    );
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Profile updated.', 'user' => $userdata)));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update user profile photo.
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateUserProfilePic() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'image'     			=> 'required',
            //'device_id'        	=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return array('status' => 'error', 'message' => $validator->errors()->all());
        } else {
            //$input_data['user_id'] = 38;
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if (Input::file('image')) {
                    $image = Input::file('image');
                    // calls image upload function
                    $filename = $this->ImageUpload($image, $folder = 'users', $resize = true, ['50x50', '100x100', '200x200', '300x300', '400x400', '500x500']);
                } else {
                    // get image name from user data.
                    $filename = isset($user['image']) ? $user['image'] : '';
                }
                // calls update profile photo function for update
                $last_id = User_model::updateprofilephoto($user_id, $filename);
                if ($last_id) {
                    $user_data = User_model::GetUserById($user['id']);
                    $userdata = array(
                        'image' => $user_data['image'],
                        'image_path' => WEBSITE_UPLOADS_URL . 'users/' . $user_data['image'],
                    );
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Profile updated.', 'user' => $userdata)));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for logout user.
     *
     * @param null
     *
     * @return response message.
     */
    public function logout() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $input_data['user_id'] = 38;
        //$this->DeleteAPItoken($this->decrypt($input_data['user_id']),$input_data['device_id'],$input_data['auth_token']);
        $this->DeleteAPI($input_data['user_id']);
        Auth::logout();
        return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'You have successfully logged out.')));
    }

    /**
     * function for change user password.
     *
     * @param null
     *
     * @return response message.
     */
    public function changepassword() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'user_id' => 'required',
            'device_id' => 'required',
            'platform_type' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $old_password = $input_data['old_password'];
            $new_password = $input_data['new_password'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if (Hash::check($old_password, $user['password'])) {
                    $result = User_model::changepassword($user_id, Hash::make($new_password));
                    if ($result) {
                        $full_name = $user['first_name'] . ' ' . $user['last_name'];
                        $subjectReplaceArray = array();
                        $bodyReplaceArray = array($full_name);
                        /* call email sending function */
                        $mail_send = $this->mail_send($action = 'reset_password', $user['email'], $full_name, $subjectReplaceArray, $bodyReplaceArray);
                        return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Password changed.')));
                    } else {
                        return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                    }
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Current password is incorrect.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function forgot password.
     *
     * @param null
     *
     * @return response with user data on success otherwise error.
     */
    public function forgotpassword() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'email' => 'required|email',
            //'device_id'        	=> 'required',
            'platform_type' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return array('status' => 'error', 'message' => $validator->errors()->all());
        } else {
            $email = $input_data['email'];
            $user = User_model::GetUserByEmail($email);
            if (!empty($user)) {
                $reset_password_token = $this->generate_random_string(20); // generate reset password token
                $url = URL::route('api_user_reset_password', $reset_password_token);
                $full_name = $user['first_name'] . ' ' . $user['last_name'];
                $subjectReplaceArray = array();
                $bodyReplaceArray = array($full_name, $url, $url);
                /* call email sending function */
                $mail_send = $this->mail_send($action = 'forgot_password', $user['email'], $full_name, $subjectReplaceArray, $bodyReplaceArray);
                if ($mail_send) {
                    User_model::SetForgotPasswordToken($user['id'], $reset_password_token);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Email has been sent please check email and follow the steps to reset password.', 'reset_password_token' => $reset_password_token, 'reset_password_url' => $url)));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Email is not registered.')));
            }
        }
    }

    /**
     * function for reset password.
     *
     * @param validate token
     *
     * @return response with user data on success otherwise error.
     */
    public function resetPassword($reset_password_token = null) {
        if ($reset_password_token != null) {
            if (Input::isMethod('post')) {
                $rules = array(
                    'newpassword' => 'required',
                    'confirmpassword' => 'required|same:newpassword',
                );
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()) {
                    $messages = $validator->messages();
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    $new_password = Input::get('newpassword');
                    $user = User_model::GetUserByResetToken($reset_password_token);
                    if (!empty($user)) {
                        $response = User_model::UpdatePassword($user['id'], Hash::make($new_password));
                        if ($response) {
                            $full_name = $user['first_name'] . ' ' . $user['last_name'];
                            $replace_array = array($full_name);
                            /* call email sending function */
                            $mail_send = $this->mail_send($action = 'reset_password', $user['email'], $full_name, $replace_array);
                            Toast::success(trans('You have successfully changed your password!'));
                            return redirect()->route('password-success');
                        } else {
                            Toast::error(trans('Technical error please try again later!'));
                            return Redirect::back();
                        }
                    } else {
                        Toast::error(trans('You link is expired please generate new link!'));
                        return Redirect::back();
                    }
                }
            } else {
                // load view
                return View::make('front.reset_password', compact('reset_password_token'));
            }
        } else {
            Toast::error(trans('You link is expired please generate new link!'));
            return Redirect::back();
        }
    }

    public function PasswordSuccess() {
        return View::make('front.thankyou');
    }

    /**
     * function for update user phone number.
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdatePrepTime() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'prep_time' => 'required',
            //'device_id'        		=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'default_status' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                // default status(off or on in the app( if user on the option in the app))
                if ($input_data['default_status'] == '1') {
                    // calls update  prep time function for update
                    if ($input_data['prep_time'] == 1) {
                        $prep_time = 24 * 60;
                    } elseif ($input_data['prep_time'] == 12) {
                        $prep_time = 12 * 60;
                    } elseif ($input_data['prep_time'] == 6) {
                        $prep_time = 6 * 60;
                    } else {
                        $prep_time = $input_data['prep_time'];
                    }
                    $update = User_model::update_prep_time($user_id, $prep_time);
                } elseif ($input_data['default_status'] == '0') {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Please enable the option first.')));
                } else {
                    $get_admin_default_preptime = AdminSettings::select('default_prep_time')->where('id', 20)->get()->toArray();
                    $prep_time = $get_admin_default_preptime[0]['default_prep_time'];
                    $update = User_model::update_prep_time($user_id, $prep_time);
                }
                if ($update) {
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Prep time updated.')));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error please try again later.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update default notification settings
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateEmailNotificationSettings() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'notification_status' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['notification_status'] == 1) {
                    // calls update email notification function for update
                    $update = User_model::update_email_notification($user_id, $input_data['notification_status']);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Successfully activated.')));
                } else {
                    $update = User_model::update_email_notification($user_id, $input_data['notification_status']);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Successfully deactivated.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    public function DefaultSettingsValues() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'device_id' => 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            //  $input_data['user_id'] = 38;
            $user_id = $input_data['user_id'];
            $default_settings_value['prep_time'][0]['name'] = '1 Day';
            $default_settings_value['prep_time'][1]['name'] = '12 Hours';
            $default_settings_value['prep_time'][2]['name'] = '6 Hours';

            // function to get default admin clockin time
            $default_clock_in_data = AdminSettings::select('clock_in_default_time')->where('id', 20)->first();
            $clock_in_default_time = $default_clock_in_data->clock_in_default_time . ' Hours';
            $default_settings_value['clock_in'][0]['name'] = 'Default';
            $default_settings_value['clock_in'][1]['name'] = '30 Minutes';
            $default_settings_value['clock_in'][2]['name'] = '1 Hour';
            $default_settings_value['clock_in'][3]['name'] = '2 Hour';

            // function to get default admin clockin time
            $default_clock_out_data = AdminSettings::select('default_time_clockout')->where('id', 20)->first();
            $clock_out_default_time = $default_clock_out_data->default_time_clockout . ' Minutes';
            $default_settings_value['clock_out'][0]['name'] = 'Clock out after default time';
            $default_settings_value['clock_out'][1]['name'] = 'Clock out after clinic schedule time';

            // leave location array
            $default_settings_value['leave_location'][0]['name'] = '30 min';
            $default_settings_value['leave_location'][1]['name'] = '60 min';
            $default_settings_value['leave_location'][2]['name'] = '120 min';

            // notification_groupBy array
            $default_settings_value['notification_groupBy'][0]['name'] = 'Right away';
            $default_settings_value['notification_groupBy'][1]['name'] = 'Every hour';
            $default_settings_value['notification_groupBy'][2]['name'] = '2 times a day';
            $default_settings_value['notification_groupBy'][3]['name'] = '1 time a day';

            // notification_groupBy array
            $default_settings_value['system_calender'][0]['name'] = 'default';
            $default_settings_value['system_calender'][1]['name'] = 'off';
            $default_settings_value['system_calender'][2]['name'] = 'sync';

            // designation array
            $default_settings_value['designation'][0]['name'] = 'W2 employee';
            $default_settings_value['designation'][1]['name'] = '1099 contractor';

            // timeformat array
            $default_settings_value['time_format'][0]['name'] = '12 Hours';
            $default_settings_value['time_format'][0]['value'] = '12';
            $default_settings_value['time_format'][1]['name'] = '24 Hours';
            $default_settings_value['time_format'][1]['value'] = '24';

            // timezone array
            $all_time_zones = TimeZoneModel::get()->toArray();
            $x = 0;
            foreach ($all_time_zones as $all_time_zone) {
                $default_settings_value['timezone'][$x]['name'] = $all_time_zone['timezone_value'];
                $default_settings_value['timezone'][$x]['value'] = $all_time_zone['timezone_name'];
                $x++;
            }
            if ($default_settings_value) {
                return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Ok', 'data' => $default_settings_value)));
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Technical error')));
            }
        }
    }

    /**
     * function for update system calender opetions
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateSystemCalender() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'system_calender_value' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['system_calender_value'] == 'default') {
                    $system_calender_value = 1;
                    $update = User_model::update_system_calender($user_id, $system_calender_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to : default')));
                } else if ($input_data['system_calender_value'] == 'off') {
                    $system_calender_value = 0;
                    $update = User_model::update_system_calender($user_id, $system_calender_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to : off')));
                } else if ($input_data['system_calender_value'] == 'sync') {
                    $system_calender_value = 2;
                    $update = User_model::update_system_calender($user_id, $system_calender_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to : sync')));
                } else {
                    return false;
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update notification group by settings of user
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateNotificationGroupBy() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'notification_groupBy_value' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['notification_groupBy_value'] == 'Right away') {
                    $notification_groupBy_value = 1;
                    $update = User_model::update_notification_group($user_id, $notification_groupBy_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to right away')));
                } else if ($input_data['notification_groupBy_value'] == 'Every hour') {
                    $notification_groupBy_value = 2;
                    $update = User_model::update_notification_group($user_id, $notification_groupBy_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to every hour')));
                } else if ($input_data['notification_groupBy_value'] == '2 times a day') {
                    $notification_groupBy_value = 3;
                    $update = User_model::update_notification_group($user_id, $notification_groupBy_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to 2 times in a day')));
                } else if ($input_data['notification_groupBy_value'] == '1 time a day') {
                    $notification_groupBy_value = 4;
                    $update = User_model::update_notification_group($user_id, $notification_groupBy_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to 1 time in a day')));
                } else {
                    return false;
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update timezone of user
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateTimezoneSetting() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'timezone_value' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                $update = User_model::update_user_timezone($user_id, $input_data['timezone_value']);
                if ($update) {
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Timezone updated')));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'error occured')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update time format of user
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateTimeFormatSetting() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        					=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'timeformat_value' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                $update = User_model::update_user_timeformat($user_id, $input_data['timeformat_value']);
                if ($update) {
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Time format updated')));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'error occured')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update clock in time settings of user
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateClockInSettings() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'clock_in_value' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['clock_in_value'] == 'Default') {
                    //  $default_clock_in_data 	= AdminSettings::select('clock_in_default_time')->where('id',20)->first();
                    //  $clock_in_default_time	= $default_clock_in_data->clock_in_default_time;
                    $update = User_model::update_clockin_setting($user_id, 'Default');
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to default time')));
                } else if ($input_data['clock_in_value'] == '30 Minutes') {
                    $clcok_in_time = 30 / 60;
                    $update = User_model::update_clockin_setting($user_id, $clcok_in_time);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to 30 minutes')));
                } else if ($input_data['clock_in_value'] == '1 Hour') {
                    $clcok_in_time = 1;
                    $update = User_model::update_clockin_setting($user_id, $clcok_in_time);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to 1 hour')));
                } else if ($input_data['clock_in_value'] == '2 Hour') {
                    $clcok_in_time = 2;
                    $update = User_model::update_clockin_setting($user_id, $clcok_in_time);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Updated to 2 hours')));
                } else {
                    return false;
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update clock out time of user
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateClockOutSettings() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'clock_out_value' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['clock_out_value'] == 1) {
                    $update = User_model::update_clockout_setting($user_id, $input_data['clock_out_value']);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Activated to default time')));
                } else if ($input_data['clock_out_value'] == 0) {
                    $update = User_model::update_clockout_setting($user_id, $input_data['clock_out_value']);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Activated to clinic scheduled time')));
                } else {
                    $update = User_model::update_clockout_setting($user_id, $input_data['clock_out_value']);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Activated to ' . $input_data['clock_out_value'] . ' minutes')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update leave location by selected amount
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateLeaveLocation() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        					=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'leave_location_value' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['leave_location_value'] != null) {
                    $location_value = $this->RemoveAlphabets($input_data['leave_location_value']);
                    $update = User_model::update_leave_location($user_id, $location_value);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Leave location updated.')));
                } else {
                    return false;
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for disable email confirmation for clinic notification
     *
     * @param null
     *
     * @return user data.
     */
    public function DisableEmailConfirmation() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'disable_status' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['disable_status'] == 1) {
                    $disable_status = 1;
                    $update = User_model::update_disable_email_status($user_id, $disable_status);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Email confirmation enabled.')));
                } else if ($input_data['disable_status'] == 0) {
                    $disable_status = 0;
                    $update = User_model::update_disable_email_status($user_id, $disable_status);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Email confirmation disabled.')));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Error occured.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for change system calender status
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdateSystemCalenderStatus() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            'device_id' => 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                if ($input_data['status'] == 1) {
                    $status = 1;
                    $update = User_model::update_system_calender_status($user_id, $status);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Activated.')));
                } else if ($input_data['status'] == 0) {
                    $status = 0;
                    $update = User_model::update_system_calender_status($user_id, $status);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Deactivated.')));
                } else {
                    return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'Error occured.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

    /**
     * function for update push notification settings
     *
     * @param null
     *
     * @return user data.
     */
    public function UpdatePushNotification() {
        $input_data = $this->GetDecryptedData(Input::get()['request']);
        $rules = array(
            //'device_id'        						=> 'required',
            'platform_type' => 'required',
            'user_id' => 'required',
            'notification_status' => 'required',
        );
        $validator = Validator::make($input_data, $rules);
        if ($validator->fails()) {
            return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
        } else {
            $user_id = $input_data['user_id'];
            $notification_status = $input_data['notification_status'];
            $user = User_model::GetUserById($user_id);
            if (!empty($user)) {
                // calls update default notification settings
                if ($notification_status == 1) {
                    // calls update push notification function for update
                    $update = User_model::update_push_notification($user_id, $notification_status);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Push notification enabled.')));
                } else {
                    $update = User_model::update_push_notification($user_id, $notification_status);
                    return $this->encrypt(json_encode(array('status' => 'success', 'message' => 'Push notification disabled.')));
                }
            } else {
                return $this->encrypt(json_encode(array('status' => 'error', 'message' => 'User not found.')));
            }
        }
    }

}
