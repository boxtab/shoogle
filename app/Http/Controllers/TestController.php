<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invite;
use App\Models\Shoogle;
use App\Repositories\TestRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Constants\RoleConstant;
use Spatie\Permission\Models\Role;
use App\Repositories\TestRepositoryInterface;

class TestController extends Controller
{
    private $testRepository;

    /**
     * TestController constructor.
     *
     * @param TestRepository $testRepository
     */
    public function __construct(TestRepository $testRepository)
    {
        $this->testRepository = $testRepository;
    }

    public function index()
    {
        $data = 'default';
        $email = 'asd@asd.com';


        if ( filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data = 'Valid email';
        } else {
            $data = 'No valid';
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
