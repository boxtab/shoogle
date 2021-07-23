<?php

namespace App\Repositories;

use App\Models\Invite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InviteRepository extends Repositories implements InviteRepositoryInterface
{
    /**
     * @var array
     */
    private $uploadResult = [];

    /**
     * @var Invite
     */
    protected $model;

    /**
     * InviteRepository constructor.
     *
     * @param Invite $invite
     */
    public function __construct(Invite $invite)
    {
        parent::__construct($invite);
    }

    /**
     * Writes a file to the invites table.
     *
     * @param array $fileCSV
     * @return string
     */
    public function upload(array $fileCSV = null)
    {
        return 'test repositories';
    }

    /**
     * Returns download results.
     *
     * @return array
     */
    public function getUploadResult()
    {
        return $this->uploadResult;
    }

}
