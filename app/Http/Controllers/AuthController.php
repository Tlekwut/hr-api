<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AuthLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|integer|unique:employees,employee_id',
                'name' => 'required|string',
                'password' => 'required|string|min:6',
                'bio_data' => 'required|integer|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            $employee = Employee::create([
                'employee_id' => $request->employee_id,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'bio_data' => Hash::make($request->bio_data),
                'points' => 0,
                'status' => 'active'
            ]);
            $token = JWTAuth::fromUser($employee);
            return response()->json([
                'message' => 'User registered successfully',
                'employee' => $employee,
                'token' => $token
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' =>  $th->getMessage()], 500);
        }
    }

    public function authLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'employee_id' => 'required|string',
                'password' => 'required|string|min:6',
            ]);

            $employee = Employee::where('employee_id', $credentials['employee_id'])->first();

            if (!$employee) {
                AuthLog::create([
                    'employee_id' => $credentials['employee_id'],
                    'status' => 'failed'
                ]);
                return response()->json(['error' => 'User not found'], 404);
            }
            if (!Hash::check($credentials['password'], $employee->password)) {
                AuthLog::create([
                    'employee_id' => $credentials['employee_id'],
                    'status' => 'failed'
                ]);
                return response()->json(['error' => 'Incorrect password'], 401);
            }

            try {
                $token = JWTAuth::fromUser($employee);
            } catch (\Throwable $th) {
                return response()->json(['error' => 'Could not create token: ' . $th->getMessage()], 500);
            }

            AuthLog::create([
                'employee_id' => $employee->employee_id,
                'status' => 'success'
            ]);

            return response()->json([
                'token' => $token,
                'user' => [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'points' => $employee->points,
                    'first_login' => $employee->first_login
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error function: ' . $th->getMessage()], 500);
        }
    }

    public function loginWithPin(Request $request)
    {
        try {
            $request->validate([
                'pin' => 'required|string|min:6|max:6',
            ]);

            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!Hash::check($request->pin, $user->bio_data)) {
                return response()->json(['error' => 'Incorrect PIN'], 401);
            }

            return response()->json([
                'message' => 'PIN login successful',
                'first_login' => $user->first_login === 1 ? true : false,
                'user' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:6',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255|unique:employees,email,' . auth()->user()->employee_id . ',employee_id',
                'address' => 'required|string',
            ]);

            $user = auth()->user();

            $Employee = Employee::where('employee_id', $user->employee_id)->first();

            if (!$Employee) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $Employee->password = Hash::make($request->password);
            $Employee->name = $request->name;
            $Employee->phone_number = $request->phone;
            $Employee->email = $request->email;
            $Employee->address = $request->address;
            $Employee->first_login = false;

            $Employee->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $Employee,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error updating profile: ' . $th->getMessage()], 500);
        }
    }

    public function updatePin(Request $request)
    {
        try {
            $request->validate([
                'pin' => 'required|string|min:6|max:6',
            ]);
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $Employee = Employee::where('employee_id', $user->employee_id)->first();

            if (!$Employee) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $Employee->bio_data = Hash::make($request->pin);
            $Employee->save();
            return response()->json([
                'message' => 'PIN update successful',
                'user' => $Employee,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
