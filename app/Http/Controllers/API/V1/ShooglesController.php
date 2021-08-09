<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoogleCreateUpdate;
use App\Http\Resources\ShooglesListResource;
use App\Http\Resources\ShooglesResource;
use App\Models\Buddie;
use App\Models\Company;
use App\Models\ModelHasRole;
use App\Models\Shoogle;
use App\Repositories\ShooglesRepository;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Class ShooglesController
 * @package App\Http\Controllers\API\V1
 */

class ShooglesController extends BaseApiController
{
    /**
     * ShooglesController constructor.
     * @param ShooglesRepository $shooglesRepository
     */
    public function __construct(ShooglesRepository $shooglesRepository)
    {
        $this->repository = $shooglesRepository;
    }

    /**
     * Display a listing of the shoogles.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->has('search') ? $request->search : null;

        $shoogles = $this->repository->getList($search);
        $shooglesListResource = new ShooglesListResource($shoogles);

        return ApiResponse::returnData($shooglesListResource);
    }

    /**
     * Show the form for creating a new shoogle.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'wellbeingCategoryId'   => ['required', 'integer', 'exists:wellbeing_categories,id'],
            'active'                => ['required', 'boolean'],
            'title'                 => ['nullable', 'min:2', 'max:45'],
            'description'           => ['nullable', 'min:2', 'max:9086'],
            'coverImage'            => ['required', 'min:2', 'max:256'],
            'acceptBuddies'         => ['required', 'boolean'],
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            Shoogle::create([
                'owner_id' => Auth()->user()->id,
                'wellbeing_category_id' => $request->wellbeingCategoryId,
                'active' => $request->active,
                'title' => $request->title,
                'reminder' => Carbon::now(),
                'description' => $request->description,
                'cover_image' => $request->coverImage,
                'accept_buddies' => $request->acceptBuddies,
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            return $this->globalError( $e->errorInfo );
        }

        return response()->json([
            'success' => true,
            'data' => [],
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
        $shoogles = Shoogle::on()->where('id', $id)->firstOrFail();

        $shooglesResource = new ShooglesResource($shoogles);
        return $shooglesResource->response();
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
                'wellbeing_category_id' => $request->wellbeingCategoryId,
                'active' => $request->active,
                'title' => $request->title,
                'description' => $request->description,
                'cover_image' => $request->coverImage,
                'accept_buddies' => $request->acceptBuddies,
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
            'data' => [],
        ]);
    }
}
