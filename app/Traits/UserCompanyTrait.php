<?php

namespace App\Traits;

use App\Models\Company;
use App\User;
use Exception;
use Illuminate\Http\Response;

/**
 * Trait UserCompanyTrait
 * @package App\Traits
 */
trait UserCompanyTrait
{
    /**
     * Users in the same company?
     *
     * @param int|null $userId1
     * @param int|null $userId2
     * @throws Exception
     */
    private function isUsersInCompany(?int $userId1, ?int $userId2)
    {
        if ( is_null($userId1) ) {
            throw new Exception('First user ID is empty.', Response::HTTP_NOT_FOUND);
        }

        if ( is_null($userId2) ) {
            throw new Exception('Second user id is empty.', Response::HTTP_NOT_FOUND);
        }

        $user1 = User::on()->where('id', '=', $userId1)->first();
        if ( is_null($user1) ) {
            throw new Exception('First user not found.', Response::HTTP_NOT_FOUND);
        }

        $user2 = User::on()->where('id', '=', $userId2)->first();
        if ( is_null($user2) ) {
            throw new Exception('Second user not found.', Response::HTTP_NOT_FOUND);
        }

        $user1CompanyId = $user1->company_id;
        if ( is_null($user1CompanyId) ) {
            throw new Exception('Company ID not found for the first user.', Response::HTTP_NOT_FOUND);
        }

        $user2CompanyId = $user2->company_id;
        if ( is_null($user2CompanyId) ) {
            throw new Exception('Company ID not found for the second user.', Response::HTTP_NOT_FOUND);
        }

        $user1Company = Company::on()->where('id', '=', $user1CompanyId)->first();
        if ( is_null($user1Company) ) {
            throw new Exception('The first user company was not found.', Response::HTTP_NOT_FOUND);
        }

        $user2Company = Company::on()->where('id', '=', $user2CompanyId)->first();
        if ( is_null($user2Company) ) {
            throw new Exception('The second users company was not found.', Response::HTTP_NOT_FOUND);
        }

        if ( $user1CompanyId !== $user2CompanyId ) {
            throw new Exception('Users are not in the same company.', Response::HTTP_FORBIDDEN);
        }
    }
}
