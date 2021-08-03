<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invite;
use App\Models\Shoogle;
use App\Repositories\InviteRepositoryInterface;
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
     * @param TestRepositoryInterface $testRepository
     */
    public function __construct(TestRepository $testRepository)
    {
        $this->testRepository = $testRepository;
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}
