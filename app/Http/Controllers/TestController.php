<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invite;
use App\Models\Shoogle;
use App\Repositories\InviteRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Constants\RoleConstant;
use Spatie\Permission\Models\Role;
use App\Repositories\TestRepositoryInterface;

class TestController extends Controller
{
    /**
     * @var TestRepositoryInterface
     */
    private $testRepository;

    /**
     * TestController constructor.
     *
     * @param TestRepositoryInterface $testRepository
     */
//    private function __construct(TestRepositoryInterface $testRepository)
//    {
//        $this->testRepository = $testRepository;
//    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}
