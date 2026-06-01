<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,teacher'],
            'teacher_id' => ['required_if:role,teacher', 'nullable', 'string', 'max:50'],
        ], [
            'teacher_id.required_if' => __('The Teacher ID is required when registering as a teacher.'),
        ]);

        $teacherIdValue = null;

        if ($request->role === 'teacher') {
            $teacherIdValue = $request->teacher_id;
        } else if ($request->role === 'student' && $request->filled('teacher_id')) {
            $teacher = User::where('role', 'teacher')
                ->where('teacher_id', $request->teacher_id)
                ->first();

            if (!$teacher) {
                return back()->withErrors([
                    'teacher_id' => __('The entered Teacher Code is invalid.')
                ])->withInput();
            }

            $teacherIdValue = $teacher->id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'teacher_id' => $teacherIdValue,
        ]);

        Auth::login($user);

        return redirect('/challenges');
    }
}
