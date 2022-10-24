<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Thread;

class ThreadRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Thread::class;
    }

    public function isThreadExists($parm)
    {
        return $this->model
            ->join('thread_users', 'threads.id', '=', 'thread_users.thread_id')
            ->whereIn('thread_users.user_id', [$parm['sender_id'], $parm['reciever_id']])
            ->get();

    }
}
