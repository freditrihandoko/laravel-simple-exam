<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;

class UsersGroupImport implements ToCollection, WithHeadingRow, WithValidation, WithStartRow
{
    private $groupId;
    private $errors = [];
    private $validRows = [];

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Importing users for group: ' . $this->groupId);
        foreach ($rows as $row) {
            Log::info('Processing row: ', $row->toArray());

            $user = User::where('email', $row['email'])->orWhere('username', $row['username'])->first();

            if ($user) {
                $group = $user->groups()->where('group_id', $this->groupId)->exists();

                if ($group) {
                    $this->errors[] = "{$row['email']} ({$row['username']}) is already in the group.";
                } else {
                    $user->groups()->attach($this->groupId);
                    $this->validRows[] = $row->toArray();
                }
            } else {
                $this->errors[] = "{$row['email']} or {$row['username']} does not exist.";
            }
        }
    }

    public function rules(): array
    {
        return [
            '*.email' => ['required', 'email'],
            '*.username' => ['required'],
            '*.name' => ['required', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.email.required' => 'Email is required.',
            '*.username.required' => 'Username is required.',
            '*.name.required' => 'Name is required.',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getValidRows()
    {
        return $this->validRows;
    }

    public function startRow(): int
    {
        return 2;
    }
}
