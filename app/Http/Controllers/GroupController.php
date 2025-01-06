<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Imports\UsersGroupImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $groups = Group::withoutAdmin()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('groups.index', compact('groups', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'group_status' => 'required|string',
        ]);

        Group::create([
            'name' => $request->group_name,
            'status' => $request->group_status,
        ]);

        return response()->json(['message' => 'Group added successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'group_status' => 'required|string',
        ]);

        $group = Group::findOrFail($id);
        $group->update([
            'name' => $request->group_name,
            'status' => $request->group_status,
        ]);

        return response()->json(['message' => 'Group updated successfully']);
    }

    public function show($id)
    {
        $group = Group::findOrFail($id);
        $users = User::whereNotIn('id', $group->users->pluck('id'))->get(); // Users not in group and (may be later setup no admin showing)
        return view('groups.show', compact('group', 'users'));
    }

    public function addUsers(Request $request, $groupId)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group = Group::findOrFail($groupId);
        $group->users()->attach($request->user_ids);

        return response()->json(['message' => 'Users added to group successfully']);
    }

    public function removeUser($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);
        $user = User::findOrFail($userId);
        $group->users()->detach($user);

        return response()->json(['message' => 'User removed from group successfully']);
    }

    public function destroy($id)
    {
        Group::destroy($id);
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully');
    }

    public function importUsers(Request $request, $groupId)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        try {
            Log::info('Starting user import for group: ' . $groupId);
            $import = new UsersGroupImport($groupId);
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();
            $validRows = $import->getValidRows();
            $validCount = count($validRows);

            Log::info('User import completed with ' . $validCount . ' valid users.');
            if (count($errors) > 0) {
                Log::warning('User import completed with errors: ', $errors);
            }

            return response()->json([
                'errors' => $errors,
                'validRows' => $validRows,
                'message' => count($errors) ? 'Some users could not be imported.' : 'Users imported successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Import Users Error: ' . $e->getMessage());
            return response()->json([
                'errors' => ['An error occurred while importing users. Please check the file format and try again.'],
                'validRows' => [],
                'message' => 'Import failed.'
            ], 500);
        }
    }
}
