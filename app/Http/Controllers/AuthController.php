<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccessRequestMail;



class AuthController extends Controller
{
    public function login(){
        return view('auth.login');
    }

    public function postLogin(Request $request){
        $emailOrName = $request->input('email');
        $password = $request->input('password');

        // Determine if input is likely an email address
        $isEmail = filter_var($emailOrName, FILTER_VALIDATE_EMAIL);

        // Define the credentials array based on input type
        if ($isEmail) {
            $credentials = ['email' => $emailOrName];
        } else {
            $credentials = ['name' => $emailOrName];
        }

        $credentials['password'] = $password;

        // Attempt authentication
        if (Auth::attempt($credentials)) {
            // Authentication successful
            $user = Auth::user();

            // Check user status
            if ($user->is_active == '1') {
                // Update last login
                $update_lastlogin = User::where('email', $user->email)
                    ->update([
                        'last_login' => now(),
                        'login_counter' => $user->login_counter + 1,
                    ]);

                // Determine the correct plant and type for redirection
                $plant = strtolower($user->plant);  // Convert to lowercase for URL consistency
                $type = strtolower($user->type);    // Convert to lowercase for URL consistency

                // Handle special cases for 'ME' type and 'Power House'
                if ($type == 'me') {
                    $type = 'me';
                } elseif ($type == 'power house') {
                    $type = 'power-house';
                }

                // Handle the case where the user has access to all plants and all types
                if ($plant == 'all' && $type == 'all') {
                    // Redirect to a default page (e.g., engine ME page)
                    return redirect('/home/engine/me');
                }

                // Handle case where only plant is 'all'
                if ($plant == 'all') {
                    return redirect("/home/{$type}");
                }

                // Handle case where only type is 'all'
                if ($type == 'all') {
                    return redirect("/home/{$plant}");
                }

                // For specific plant and type, redirect to their respective dashboard
                return redirect("/home/{$plant}/{$type}");
            }
            else {
                // User is not active, redirect with message
                return redirect('/')->with('statusLogin', 'Give Access First to User');
            }
        } else {
            // Authentication failed, redirect with message
            return redirect('/')->with('statusLogin', 'Wrong Email/Name or Password');
        }
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('statusLogout','Success Logout');
    }

    public function requestAccess(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'purpose' => 'required|string|max:255',
        ]);

        // Send the email
        Mail::to(['aditia@ptmkm.co.id','muhammad.taufik@ptmkm.co.id','bayu@ptmkm.co.id'])
            /* ->cc('bayu@ptmkm.co.id') */
            ->send(new AccessRequestMail($request->all()));

        // Optionally, you can flash a success message or redirect to a specific page
        return back()->with('statusLogin', 'Your request has been submitted.');
    }
}
