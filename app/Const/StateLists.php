<?php
namespace App\Const;

class StateLists
{
    /**
     * @param ACTIVE teacher is in an active state.
     * @param REMOVED teacher has been removed by moderator.
     */
    public const TEACHER = [
        'ACTIVE' => 'active',
        'REMOVED' => 'removed',
    ];

    /**
     * @param ACTIVE course is in an active state.
     * @param REMOVED course has been removed by moderator.
     * @param FINISHED course has finished it's period.
     * @param  HOLD teacher has been removed, but course hasn't been assigned to other teacher.
     */
    public const COURSE = [
        'ACTIVE' => 'active',
        'REMOVED' => 'removed',
        'FINISHED' => 'finished',
        'HOLD' => 'hold',
    ];

    /**
     * @param ACTIVE group is in an active state.
     * @param REMOVED group has been removed by moderator.
     * @param FINISHED group has finished the course's period.
     */
    public const GROUP = [
        'ACTIVE' => 'active',
        'REMOVED' => 'removed',
        'FINISHED' => 'finished',
    ];

    /**
     * @param ACTIVE student is in an active state.
     * @param REMOVED student has been removed by moderator.
     * @param BLACKLISTED student has been blacklisted due to payment issues.
     */
    public const STUDENT = [
        'ACTIVE' => 'active',
        'REMOVED' => 'removed',
        'BLACKLISTED' => 'blacklisted',
    ];

    /**
     * @param PAID student has paid the course.
     * @param PENDING student has not paid the course yet.
     * @param EXCEEDED the course period has finished, but student not paid yet.
     */
    public const PAYMENT = [
        'PAID' => 'paid',
        'PENDING' => 'pending',
        'EXCEEDED' => 'exceeded',
    ];

    /**
     * @param MONTHLY the course is paid on a monthly basis.
     * @param ONE_TIME the course is paid only one-time.
     */
    public const PAYMENT_TYPE = [
        'MONTHLY' => 'monthly',
        'ONE_TIME' => 'one-time',
    ];
}
