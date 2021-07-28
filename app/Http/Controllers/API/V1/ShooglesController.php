<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoogleCreateUpdate;
use App\Models\Buddie;
use App\Models\Company;
use App\Models\ModelHasRole;
use App\Models\Shoogle;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShooglesController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info($request->query);

        $data = Shoogle::on()
//            ->when( ! is_null( $request->query ) , function ($query) use ($request) {
//                return $query->where('title', 'like', '%' . (string)$request->query . '%');
//            })
            ->get(['id', 'title'])
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'wellbeing_category_id' => ['required', 'integer', 'exists:wellbeing_categories,id'],
            'active' => ['required', 'boolean'],
            'title' => ['nullable', 'min:2', 'max:45'],
            'description' => ['nullable', 'min:2', 'max:9086'],
            'cover_image' => ['required', 'min:2', 'max:256'],
            'accept_buddies' => ['required', 'boolean'],
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            Shoogle::create([
                'owner_id' => Auth()->user()->id,
                'wellbeing_category_id' => $request->wellbeing_category_id,
                'active' => $request->active,
                'title' => $request->title,
                'reminder' => Carbon::now(),
                'description' => $request->description,
                'cover_image' => $request->cover_image,
                'accept_buddies' => $request->accept_buddies,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->globalError( $e->errorInfo );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'The shoogle was created successfully',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id = null)
    {
        try {
            $data = Shoogle::where('id', $id)
                ->firstOrFail()
                ->get(['id', 'title', 'created_at', 'owner_id'])
                ->map( function ( $item ) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'created_at' => $item->created_at,
                        'creator' => [
                            'email' => User::where('id', $item->owner_id)->first()->email,
                            'team' => User::where('id', $item->owner_id)->first()->company->name,
                            'role' => ModelHasRole::where('model_id', $item->owner_id)->first()->role->name,
                        ],
                        'shooglers_count' => Shoogle::count(),
                        'buddies_count' => Buddie::where('shoogle_id', $item->id)->count(),
                    ];
                })->toArray();;

        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        // { id, title, creator: { email, team, role }, wellbeing_category, created_at, shooglers_count, buddies_count }}

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ShoogleCreateUpdate $request, $id)
    {
        try {
            $shoogle = Shoogle::where('id', $id)->firstOrFail();
            $shoogle->update([
                'wellbeing_category_id' => $request->wellbeing_category_id,
                'active' => $request->active,
                'title' => $request->title,
                'description' => $request->description,
                'cover_image' => $request->cover_image,
                'accept_buddies' => $request->accept_buddies,
            ]);
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => $shoogle,
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
            $shoogle = Shoogle::find($id);
            $shoogle->delete();
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Shoogle successfully deleted',
            ],
        ]);
    }
}
