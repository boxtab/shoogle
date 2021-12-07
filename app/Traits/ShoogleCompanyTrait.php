<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Models\Company;
use App\Models\Shoogle;
use App\Scopes\ShoogleScope;
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
     * @param bool $blocked
     * @throws Exception
     */
    private function checkCreatorAndUserInCompany(?int $shoogleId, bool $blocked = false)
    {
        $currentUserCompanyId = HelperCompany::getCompanyId();
        if (is_null($currentUserCompanyId)) {
            throw new Exception('The company ID for the current user was not found.', Response::HTTP_NOT_FOUND);
        }

        if ( is_null($shoogleId) ) {
            throw new Exception('Shoogle ID not found.', Response::HTTP_NOT_FOUND);
        }

        if ( $blocked === false ) {
            $shoogle = Shoogle::on()->find($shoogleId);
        } else {
            $shoogle = Shoogle::on()
                ->withoutGlobalScope(ShoogleScope::class)
                ->find($shoogleId);
        }

        if ( is_null( $shoogle ) ) {
            throw new \Exception('Shoogle not found for this ID.', Response::HTTP_NOT_FOUND);
        }

        $ownerUser = User::on()
            ->where('id', '=', $shoogle->owner_id)
            ->first();
        if (is_null($ownerUser)) {
            throw new Exception('Company owner not found.', Response::HTTP_NOT_FOUND);
        }

        $ownerUserCompanyId = $ownerUser->company_id;
        if ( is_null($ownerUserCompanyId) ) {
            throw new Exception('The creator of shoogle has no company.', Response::HTTP_NOT_FOUND);
        }

        $ownerCompany = Company::on()->where('id', '=', $ownerUserCompanyId)->first();
        if ( is_null($ownerCompany) ) {
            throw new Exception('The company of the creator of shoogle was not found in the table.', Response::HTTP_NOT_FOUND);
        }

        if ($currentUserCompanyId !== $ownerUserCompanyId) {
            throw new Exception('The requested shoogle is not part of your company.', Response::HTTP_FORBIDDEN);
        }
    }
}
