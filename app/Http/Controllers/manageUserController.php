<?php

namespace App\Http\Controllers;

use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\User;
use App\Models\users_address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Type\Integer;

class manageUserController extends Controller
{

    public function viewUsers()
    {
        $patients = User::where('role', 'patient')->where('status','!=','archived')->orderBy('id', 'ASC')->get();
        return view('manageUsers.manageUsers', ['isActive' => true, 'page' => 'MANAGE USERS', 'patients' => $patients]);
    }
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'first_name' => [
                    'required',
                    Rule::unique('users')->where(function ($query) use ($request) {
                        return $query->where('first_name', $request->first_name)
                            ->where('last_name', $request->last_name);
                    })
                ],
                'middle_initial' => 'sometimes|nullable|string',
                'last_name' => ['required'],
                'contact_number' => 'required|digits_between:7,12',
                'date_of_birth' => 'required|date',
                'patient_type' => 'required',
                'blk_n_street' => 'required',
                'patient_purok_dropdown' => 'required',
                'add_suffix' => 'sometimes|nullable|string'

            ]);
           
            $data['password'] = Hash::make($data['password']);

           
            switch ('patient') {
                case 'nurse':
                case 'staff':
                    $data['status'] = 'pending';  // Needs admin or nurse approval
                    break;

                case 'patient':
                    $data['status'] = 'active';   // Patients get access immediately
                    break;

                default:
                    $data['status'] = 'pending';  // fallback
                    break;
            }

            $fullAddress = implode(' ', array_filter([
                $data['blk_n_street'] ?? null,
                $data['patient_purok_dropdown'] ?? null,
                'Hugo Perez,',
                'Trece Martires City,',
                'Cavite'
            ]));

            $data['full_address'] = $fullAddress;

            $newUser = User::create([
                
                'email' => $data['email'],
                'patient_type' => $data['patient_type'],
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'],
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
                'contact_number' => $data['contact_number'],
                'address' => $fullAddress,
                'status' => 'active',
                'password' => $data['password'],
                'role' => 'patient',
                'suffix' => $data['add_suffix']??''
            ]);

           
            //    add the user address
            // dd($patient->id);
            $blk_n_street = explode(',', $data['blk_n_street']);
            // dd($blk_n_street);
            users_address::create([
                'user_id' => $newUser->id,
                'house_number' => $blk_n_street[0] ?? $data['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $data['patient_purok_dropdown'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
            ]);

            return response()->json(['message' => 'Patient Account is successfully created'], 201);
        } catch (ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function info($id)
    {
        $user = User::findOrFail((int)$id);

        $patient = patients::where('user_id', (int)$id)->firstOrFail();
        $address = patient_addresses::where('patient_id', $patient->id)->first();

        // combined all of the data of the patient user
        $combined = array_merge(['user' => $user->toArray(), 'patient' => $patient->toArray(), 'patient_address' => $address->toArray()]);

        return response()->json(['response' => $combined]);
    }

    public  function updateInfo(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validate([
                'first_name' => 'sometimes|nullable|string',
                'last_name' => 'sometimes|nullable|string',
                'middle_initial' => 'sometimes|nullable|string|max:2',
                'age' => 'sometimes|nullable|numeric',
                'date_of_birth' => 'sometimes|nullable|date',
                'sex' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                
                'email' => ['required', 'email'],
                'blk_n_street' => 'required',
                'patient_purok_dropdown' => 'required',
                'password' => ['sometimes', 'nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'profile_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            // check if there is new password
            if (!empty($data['password'])) {
                $user->update([
                    
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']) // hash the password
                ]);
            } else {
                $user->update([
                    
                    'email' => $data['email'],
                ]);
            }

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');

                // Generate unique filename
                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('images/profile_images');

                // Make sure the folder exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Delete the old image if it exists and is different
                $profileImagePath = $user->role == 'staff' ? $user->staff : $user->nurses;
                if (!empty($profileImagePath->profile_image) && $profileImagePath->profile_image !== 'images/default_profile.png') {
                    $oldImagePath = public_path(ltrim($profileImagePath->profile_image, '/'));
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete old file
                    }
                }

                // Move the new file
                $file->move($destinationPath, $filename);

                // Update staff profile image path in DB

                $user->patient->profile_image = 'images/profile_images/' . $filename;
                $user->patient->save();
            }

            // update the patient
            $patient = $user->patient;
            $patient->update([
                'first_name' => $data['first_name'] ?? null,
                'middle_initial' => $data['middle_initial'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'age' => $data['age'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'sex' => $data['sex'] ?? null,
                'civil_status' => $data['civil_status'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'nationality' => $data['nationality'] ?? null,
            ]);

            $blk_n_street = explode(',', $data['blk_n_street'], 2); // limit to 2 parts

            $house_number = trim($blk_n_street[0] ?? '');
            $street = trim($blk_n_street[1] ?? null); // allow null if street not provided

            $patient->address->update([
                'house_number' => $house_number,
                'street' => $street,
                'purok' => $data['patient_purok_dropdown']
            ]);

            return response()->json(['success' => 'Patient information has been updated']);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function remove($id)
    {
        $user = User::findorFail($id);

        $user->update([
            'status' => 'archived'
        ]);

        return response()->json(['message' => 'Health worker deleted successfully.']);
    }
}
