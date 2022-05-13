<?php
namespace App\Const;

use DateTime;

trait RemoveModelTrait
{
    public function remove()
    {
        $this->update([
            'state' => 'removed',
            'archived' => true,
            'archived_at' => new DateTime(),
        ]);
    }
}
