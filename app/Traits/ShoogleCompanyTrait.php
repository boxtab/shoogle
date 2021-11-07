<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Models\Shoogle;
use App\User;
use Illuminate\Http\Response;
use Exception;

/**
 * Trait ShoogleCompanyTrait
 * @package App\Traits
 */
trait ShoogleCompanyTrait
{
    /**
     * Check creator and user in the same company.
     *
     * @param int|null $shoogleId
     * @throws Exception
     */
    private function checkCreatorAndUserInCompany(?int $shoogleId)
    {
        $currentUserCompanyId = HelperCompany::getCompanyId();
        if (is_null($currentUserCompanyId)) {
            throw new Exception('The company ID for the current user was not found.', Response::HTTP_NOT_FOUND);
        }

        if ( is_null($shoogleId) ) {
            throw new Exception('Shoogle ID not found.', Response::HTTP_NOT_FOUND);
        }

        $shoogle = Shoogle::on()->find($shoogleId);
        if ( is_null( $shoogle ) ) {
            throw new \Exception('Shoogle not found for this ID.', Response::HTTP_NOT_FOUND);
        }

        $ownerUser = User::on()
            ->where('id', '=', $shoogle->owner_id)
            ->first();
        if (is_null($ownerUser)) {
            throw new Exception('Company owner not found.', Response::HTTP_NOT_FOUND);
        }

        if ($currentUserCompanyId !== $ownerUser->company_id) {
            throw new Exception('The requested shoogle is not part of your company.', Response::HTTP_FORBIDDEN);
        }
    }
}
