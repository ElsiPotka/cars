<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;


use App\Http\Requests\AddEmployeeRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\UpdateEmployeeJobPositionRequest;
use App\Models\Company;
use App\Models\EmployeeJobPosition;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * Store a newly created company in storage.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());

        return response()->json($company, 201);
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): JsonResponse
    {
        return response()->json($company);
    }

    /**
     * Update the specified company in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());

        return response()->json($company);
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

    /**
     * Get employees of a company.
     */
    public function getEmployees(Company $company): JsonResponse
    {
        $employees = $company->employees()->with(['user', 'role'])->paginate(15);

        return response()->json($employees);
    }

    /**
     * Add an employee to a company.
     */
    public function addEmployee(AddEmployeeRequest $request, Company $company): JsonResponse
    {
        $validated = $request->validated();

        $employee = EmployeeJobPosition::create([
            'company_id' => $company->id,
            'user_id' => $validated['user_id'],
            'role_id' => $validated['role_id'],
        ]);

        return response()->json($employee, 201);
    }

    /**
     * Update an employee's role in a company.
     */
    public function updateEmployee(UpdateEmployeeJobPositionRequest $request, Company $company, User $employee): JsonResponse
    {
        $jobPosition = EmployeeJobPosition::where('company_id', $company->id)
            ->where('user_id', $employee->id)
            ->firstOrFail();

        $jobPosition->update($request->validated());

        return response()->json($jobPosition);
    }

    /**
     * Remove an employee from a company.
     */
    public function removeEmployee(Company $company, User $employee): JsonResponse
    {
         $jobPosition = EmployeeJobPosition::where('company_id', $company->id)
            ->where('user_id', $employee->id)
            ->firstOrFail();

        $jobPosition->delete();

        return response()->json(['message' => 'Employee removed from company successfully']);
    }
}
