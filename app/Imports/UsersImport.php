<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\ValidationException;

class UsersImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $validRows = [];
    private $invalidRows = [];

    public function model(array $row)
    {
        // Check if the user already exists
        if (User::where('email', $row['email'])->exists() || User::where('username', $row['username'])->exists()) {
            $this->invalidRows[] = [
                'row' => $row,
                'errors' => ['email or username already exists']
            ];
            return null;
        }

        $this->validRows[] = $row;

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'username' => $row['username'],
            'password' => Hash::make($row['password']),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.email' => 'required|email|unique:users,email',
            '*.username' => 'required|string|alpha_dash|unique:users,username',
            '*.name' => 'required|string|max:255',
            '*.password' => 'required|string|min:8',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => ':input email is already taken.',
            'username.required' => 'The username is required.',
            'username.alpha_dash' => 'The username must only contain letters, numbers, dashes, and underscores.',
            'username.unique' => ':input username is already taken.',
            'name.required' => 'The name is required.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }

    public function getValidRows()
    {
        return $this->validRows;
    }

    public function getInvalidRows()
    {
        return $this->invalidRows;
    }
}
