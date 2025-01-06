<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\Failure;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('users.index', compact('users', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|string|email|max:255|unique:users,email',
            'user_username' => ['required', 'string', 'max:255', 'alpha_dash:ascii', 'unique:users,username'],
            'user_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'username' => $request->user_username,
                'password' => Hash::make($request->user_password),
            ]);

            return response()->json(['message' => 'User created successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating user: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'user_username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'user_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->user_name;
        $user->email = $request->user_email;
        $user->username = $request->user_username;


        if ($request->filled('user_password')) {
            $user->password = Hash::make($request->user_password);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully']);
    }

    public function show(User $user)
    {
        $groups = Group::withoutAdmin()->get();
        $userGroups = $user->groups;
        $examResults = $user->examResults;

        return view('users.show', compact('user', 'groups', 'userGroups', 'examResults'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function addToGroup(Request $request, User $user)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $user->groups()->attach($request->group_id);

        return response()->json(['message' => 'User added to group successfully']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new UsersImport;
            $import->import($request->file('file'));

            $validRows = $import->getValidRows();
            $invalidRows = $import->failures()->isNotEmpty() ? $import->failures() : $import->getInvalidRows();

            $errors = [];
            foreach ($invalidRows as $failure) {
                if (is_array($failure)) {
                    $errors[] = $failure['row']['email'] . ' ' . implode(', ', $failure['errors']);
                } else {
                    foreach ($failure->errors() as $error) {
                        $errors[] = $failure->values()[$failure->attribute()] . ' ' . $error;
                    }
                }
            }

            return response()->json([
                'message' => 'Users imported successfully',
                'validRows' => $validRows,
                'errors' => $errors,
            ], $errors ? 422 : 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error importing users: ' . $e->getMessage()], 500);
        }
    }
}
