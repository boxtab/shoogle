<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Hello, world!'
            ],
        ]);

        /*
        $validator =  Validator::make($request->all(),[
            'order' => [
                'required',
                Rule::in(['asc', 'desc', 'ASC', 'DESC']),
            ],
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            $data = Company::orderBy('name', $request->order)
                ->get()
                ->map( function ( $item ) {
                    return [ 'id' => $item->id, 'name' => $item->name ];
                })->toArray();

        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
        */
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $data = Company::where('id', $id)->firstOrFail();
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'name' => 'required|unique:companies,name|min:2|max:45'
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            $company = Company::create([
                'name' => $request->name,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->globalError( $e->errorInfo );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $company->id,
                'name' => $company->name,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(),[
            'name' => 'required|min:2|max:45'
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            $company = Company::where('id', $id)->firstOrFail();
            $company->update([
                'name' => $request->name,
            ]);
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $company->id,
                'name' => $company->name,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Company successfully deleted',
            ],
        ]);
    }
}
