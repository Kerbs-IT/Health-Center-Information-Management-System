<?php

namespace App\Http\Controllers;

use App\Models\vaccines;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class vaccineController extends Controller
{
    //
    public function getVaccines(){
        try{
            $vaccines = vaccines::get();

            return response() -> json([
                'vaccines'=> $vaccines
            ],200);
        }catch(\Exception $e){
            return response()-> json([
                'error'=> $e->getMessage()
            ],500);
        }
       
    }
    // =========================================================================
    // GET active vaccines only (for dropdowns in add vaccination form)
    // =========================================================================
    public function getActiveVaccines()
    {
        try {
            $vaccines = vaccines::active()->orderBy('type_of_vaccine')->get();

            return response()->json([
                'vaccines' => $vaccines
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // STORE - Add new vaccine
    // =========================================================================
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'type_of_vaccine' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('vaccines', 'type_of_vaccine'),
                ],
                'vaccine_acronym' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('vaccines', 'vaccine_acronym'),
                ],
                'max_doses' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:3',
                ],
            ], [
                'type_of_vaccine.required'  => 'The vaccine name is required.',
                'type_of_vaccine.unique'    => 'This vaccine name already exists.',
                'vaccine_acronym.required'  => 'The vaccine acronym is required.',
                'vaccine_acronym.unique'    => 'This vaccine acronym already exists.',
                'max_doses.required'        => 'The maximum doses field is required.',
                'max_doses.min'             => 'Minimum dose is 1.',
                'max_doses.max'             => 'Maximum dose allowed is 3.',
            ]);

            $vaccine = vaccines::create([
                'type_of_vaccine' => trim($data['type_of_vaccine']),
                'vaccine_acronym' => strtoupper(trim($data['vaccine_acronym'])),
                'max_doses'       => $data['max_doses'],
                'status'          => 'Active',
            ]);

            return response()->json([
                'message' => 'Vaccine added successfully.',
                'vaccine' => $vaccine,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Vaccine Store Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']],
            ], 500);
        }
    }

    // =========================================================================
    // UPDATE - Edit existing vaccine
    // =========================================================================
    public function update(Request $request, $id)
    {
        try {
            $vaccine = vaccines::findOrFail($id);

            $data = $request->validate([
                'type_of_vaccine' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('vaccines', 'type_of_vaccine')->ignore($id),
                ],
                'vaccine_acronym' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('vaccines', 'vaccine_acronym')->ignore($id),
                ],
                'max_doses' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:3',
                ],
            ], [
                'type_of_vaccine.required'  => 'The vaccine name is required.',
                'type_of_vaccine.unique'    => 'This vaccine name already exists.',
                'vaccine_acronym.required'  => 'The vaccine acronym is required.',
                'vaccine_acronym.unique'    => 'This vaccine acronym already exists.',
                'max_doses.required'        => 'The maximum doses field is required.',
                'max_doses.min'             => 'Minimum dose is 1.',
                'max_doses.max'             => 'Maximum dose allowed is 3.',
            ]);

            $vaccine->update([
                'type_of_vaccine' => trim($data['type_of_vaccine']),
                'vaccine_acronym' => strtoupper(trim($data['vaccine_acronym'])),
                'max_doses'       => $data['max_doses'],
            ]);

            return response()->json([
                'message' => 'Vaccine updated successfully.',
                'vaccine' => $vaccine->fresh(),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vaccine not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Vaccine Update Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']],
            ], 500);
        }
    }

    // =========================================================================
    // ARCHIVE - Soft archive (does NOT affect past records)
    // =========================================================================
    public function archive($id)
    {
        try {
            $vaccine = vaccines::findOrFail($id);

            if ($vaccine->status === 'Archived') {
                return response()->json([
                    'message' => 'Vaccine is already archived.',
                ], 422);
            }

            $vaccine->update(['status' => 'Archived']);

            return response()->json([
                'message' => 'Vaccine archived successfully. Past records remain unaffected.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vaccine not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Vaccine Archive Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']],
            ], 500);
        }
    }

    // =========================================================================
    // RESTORE - Unarchive a vaccine
    // =========================================================================
    public function restore($id)
    {
        try {
            $vaccine = vaccines::findOrFail($id);

            if ($vaccine->status === 'Active') {
                return response()->json([
                    'message' => 'Vaccine is already active.',
                ], 422);
            }

            $vaccine->update(['status' => 'Active']);

            return response()->json([
                'message' => 'Vaccine restored successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vaccine not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Vaccine Restore Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']],
            ], 500);
        }
    }
}
