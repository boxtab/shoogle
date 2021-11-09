<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Models\Shoogle;
use Illuminate\Http\Response;
use Exception;

/**
 * Trait InviteCompanyTrait
 * @package App\Traits
 */
trait InviteCompanyTrait
{
    /**
     * The creator of the invite and the current user must be from the same company.
     *
     * @param int|null $inviteId
     * @throws Exception
     */
    private function checkCreatorInviteAndUserInCompany(?int $inviteId)
    {
        $currentUserCompanyId = HelperCompany::getCompanyId();
        if (is_null($currentUserCompanyId)) {
            throw new Exception('The company ID for the current user was not found.', Response::HTTP_NOT_FOUND);
        }

        if ( is_null($inviteId) ) {
            throw new Exception('Invitation ID not found.', Response::HTTP_NOT_FOUND);
        }

        $invite = Shoogle::on()->find($inviteId);
        if ( is_null( $invite ) ) {
            throw new \Exception('Invite not found for this ID.', Response::HTTP_NOT_FOUND);
        }

        $inviteCompanyId = $invite->company_id;
        if ( is_null($inviteCompanyId) ) {
            throw new Exception('The company ID was not found in the invitation.', Response::HTTP_NOT_FOUND);
        }

        if ( $currentUserCompanyId !== $inviteCompanyId ) {
            throw new Exception('The requested invite is not included in your company.', Response::HTTP_FORBIDDEN);
        }
    }
}
