<?php

namespace App\Http\Resources;

use App\Helpers\HelperCompany;
use App\Models\Shoogle;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class ShooglesSearchResultResource
 * @package App\Http\Resources
 */
class ShooglesSearchResultResource extends JsonResource
{
    /**
     * Number of results found.
     *
     * @var int
     */
    protected $findCount;

    /**
     * Community count.
     *
     * @var int
     */
    protected $communityCount;

    /**
     * Buddies count.
     *
     * @var int
     */
    protected $buddiesCount;

    /**
     * Solos count.
     *
     * @var int
     */
    protected $solosCount;

    /**
     * Total in the database.
     *
     * @return int
     */
    public function getCount()
    {
        $companyId = HelperCompany::getCompanyId();
        if ( is_null($companyId) ) {
            return 'Unable to determine the company ID of the current user';
        } else {
            return Shoogle::on()
                ->whereHas('owner', function ($query) use ($companyId) {
                    $query->where('company_id', '=', $companyId);
                })
                ->count();
        }
    }

    /**
     * Number of results found.
     *
     * @param int $findCount
     * @return $this
     */
    public function setFindCount(int $findCount)
    {
        $this->findCount = $findCount;
        return $this;
    }

    /**
     * Community count.
     *
     * @param int $communityCount
     * @return $this
     */
    public function setCommunityCount(int $communityCount)
    {
        $this->communityCount = $communityCount;
        return $this;
    }

    /**
     * Buddies count.
     *
     * @param int $buddiesCount
     * @return $this
     */
    public function setBuddiesCount(int $buddiesCount)
    {
        $this->buddiesCount = $buddiesCount;
        return $this;
    }

    /**
     * Solos count.
     *
     * @param int $solosCount
     * @return $this
     */
    public function setSolosCount(int $solosCount)
    {
        $this->solosCount = $solosCount;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'items' => ShooglesSearchItemsResource::collection($this->resource),

            'count' => $this->getCount(),
            'findCount' => $this->findCount,
            'communityCount' => $this->communityCount,
            'buddiesCount' => $this->buddiesCount,
            'solosCount' => $this->solosCount,
        ];
    }
}
