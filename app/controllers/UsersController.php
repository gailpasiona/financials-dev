<?php



/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class UsersController extends Controller
{

    /**
     * Displays the form for account creation
     *
     * @return  Illuminate\Http\Response
     */
    public function create()
    {
        return View::make(Config::get('confide::signup_form'))->with('user', Confide::user()->username);
    }
 
    /* load user registration form */
    public function loadform(){
        return View::make('templates.modals.users');
    }

    public function load_users(){
        $users = User::with(array('roles' => function($query){
            $query->select('name');
        }))->get(array('id','full_name','username'));//User::select('full_name','username')->get();
        return Response::json($users->toArray());
    }

    public function modify($user_id){
         $user = User::select('id','username','full_name')->where('id', '=', $user_id)->first();
        // $user = User::find($user_id);
        return View::make('templates.modals.users')->with('info', $user);//Response::json($user); 
    }

    public function update_user(){
        // $user = User::findOrFail(Input::get('id'));
        // if(is_null(Input::get('password'))) {
        //     $user->username = Input::get('username');
        //     $user->full_name = Input::get('full_name');
        // } else {
        //    $user->password = Input::get('password');
        //    $user->password_confirmation = Input::get('password_confirmation');
        // }
        
        // if($user->save()){
        //     return Response::json($user);
        // }
        // else{
        //     $error = $user->errors()->all(':message');
        //     return Response::json(array('status' => '0', 'error' => json_encode($error)));
        // }
        $repo = App::make('UserRepository');
        $user = $repo->update(Input::all());
        if($user->errors()->all(':message') != null){
            $error = $user->errors()->all(':message');
            return Response::json(array('status' => '0', 'error' => json_encode($error)));
        } 
        else{
           return Response::json(array('status' => '1', 'message' => Lang::get('confide::confide.alerts.account_updated')));
        }
 
        
    }

    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function store()
    {
        $repo = App::make('UserRepository');
        $user = $repo->signup(Input::all());

        if ($user->id) {
            // if (Config::get('confide::signup_email')) {
            //     Mail::queueOn(
            //         Config::get('confide::email_queue'),
            //         Config::get('confide::email_account_confirmation'),
            //         compact('user'),
            //         function ($message) use ($user) {
            //             $message
            //                 ->to($user->email, $user->username)
            //                 ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
            //         }
            //     );
            // }

            // return Redirect::action('UsersController@login')
            //     ->with('notice', Lang::get('confide::confide.alerts.account_created'));
            return Response::json(array('status' => '1', 'message' => Lang::get('confide::confide.alerts.account_created')));
        } else {
            $error = $user->errors()->all(':message');

            // return Redirect::action('UsersController@create')
            //     ->withInput(Input::except('password'))
            //     ->with('error', $error);
            return Response::json(array('status' => '0', 'error' => json_encode($error)));
        }
    }

    /**
     * Displays the login form
     *
     * @return  Illuminate\Http\Response
     */
    public function login()
    {
        if (Confide::user()) {
           // return Redirect::to('/');
            $user = User::find(Confide::user()->id);
            $role = Role::where('name','Administrator')->first();
            if($user->hasRole($role->name)) return Redirect::to('manager');
            else return Redirect::to('financials');

        } else {
            return View::make(Config::get('confide::login_form'));
        }
    }

    /**
     * Attempt to do login
     *
     * @return  Illuminate\Http\Response
     */
    public function doLogin()
    {
        $repo = App::make('UserRepository');
        $input = Input::all();

        if ($repo->login($input)) {
            $user = User::find(Confide::user()->id);
            $role = Role::where('name','Administrator')->first();
            if($user->hasRole($role->name)) return Redirect::to('manager');
            else return Redirect::intended('financials');
            
        } else {
            if ($repo->isThrottled($input)) {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            } elseif ($repo->existsButNotConfirmed($input)) {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            } else {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return Redirect::action('UsersController@login')
                ->withInput(Input::except('password'))
                ->with('error', $err_msg);
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  Illuminate\Http\Response
     */
    public function confirm($code)
    {
        if (Confide::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('UsersController@login')
                ->with('error', $error_msg);
        }
    }

    /**
     * Displays the forgot password form
     *
     * @return  Illuminate\Http\Response
     */
    public function forgotPassword()
    {
        return View::make(Config::get('confide::forgot_password_form'));
    }

    /**
     * Attempt to send change password link to the given email
     *
     * @return  Illuminate\Http\Response
     */
    public function doForgotPassword()
    {
        if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
            return Redirect::action('UsersController@doForgotPassword')
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Shows the change password form with the given token
     *
     * @param  string $token
     *
     * @return  Illuminate\Http\Response
     */
    public function resetPassword($token)
    {
        return View::make(Config::get('confide::reset_password_form'))
                ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     *
     * @return  Illuminate\Http\Response
     */
    public function doResetPassword()
    {
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 =>Input::get('token'),
            'password'              =>Input::get('password'),
            'password_confirmation' =>Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('UsersController@resetPassword', array('token'=>$input['token']))
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return  Illuminate\Http\Response
     */
    public function logout()
    {
        if(Session::has('company')){
            Session::forget('company');
        }
        
        Confide::logout();

        return Redirect::to('login');
    }

    public function test(){
        $response = array();
        $arr1 = array("full_name" => 'Ramon Pepano', "username" => 'ramonizer', "roles" => 'user');
        $arr2 = array("full_name" => 'Ramons Pepanos', "username" => 'ramonizers', "roles" => 'users');
        array_push($response, $arr1);
        array_push($response, $arr2);
        return Response::json($response);
    }
}
