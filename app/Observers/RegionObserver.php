<?php

namespace App\Observers;

use App\Models\Region;

class RegionObserver
{
    /**
     * Handle the Region "created" event.
     */
    public function creating(Region $region): void
    {
        logger('--触发creating--');
        // 如果创建的是一个根类目
        if ((int)$region->parent_id === 0) {
            logger('11111');
            // 将层级设为 0
            $region->level = 0;
            // 将 path 设为 -
            $region->path = '-';
        } else {
            logger('2222');
            // 将层级设为父类目的层级 + 1
            $region->level = $region->parent->level + 1;
            // 将 path 值设为父类目的 path 追加父类目 ID 以及最后跟上一个 - 分隔符
            $region->path = $region->parent->path . $region->parent_id . '-';
        }
    }

    /**
     * Handle the Region "updated" event.
     */
    public function updated(Region $region): void
    {
        //
    }

    /**
     * Handle the Region "deleted" event.
     */
    public function deleted(Region $region): void
    {
        //
    }

    /**
     * Handle the Region "restored" event.
     */
    public function restored(Region $region): void
    {
        //
    }

    /**
     * Handle the Region "force deleted" event.
     */
    public function forceDeleted(Region $region): void
    {
        //
    }
}
