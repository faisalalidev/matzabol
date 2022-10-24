<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Chat;
use DB;

class ChatRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Chat::class;
    }

    public function deleteByThreadId($data)
    {

        //$this-> model->foreign('id')->references('chat_id')->on('chat_reads')->onDelete('cascade');
//DELETE FROM chat_reads WHERE chat_id IN (SELECT Id FROM chats WHERE thread_id = 1 AND sender_id = 57)

        DB::delete('DELETE FROM chat_reads WHERE chat_id IN (SELECT Id FROM chats WHERE thread_id = ' . $data['thread_id'] . ' AND sender_id = ' . $data['sender_id'] . ')');

        return $this->model
            ->where([
                ['thread_id', '=', $data['thread_id']],
                ['sender_id', '=', $data['sender_id']],
            ])
            ->delete();
    }

    public function deleteMessagesByUser($id)
    {
        DB::delete('DELETE FROM chat_reads WHERE chat_id IN (SELECT Id FROM chats WHERE sender_id = ' . $id . ')');
        return $this->model
            ->where([
                ['sender_id', '=', $id]
            ])
            ->delete();
    }

    public function getUnreadMessageCount()
    {

        /*$res = DB::select('SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON c.thread_id = tu.thread_id LEFT JOIN user_reports ur ON (reciever_id=c.sender_id AND ur.sender_id=?) OR (ur.sender_id=c.sender_id AND reciever_id=?) WHERE cr.type <> \'read_by_user\' AND c.sender_id <> ? AND tu.user_id = ? AND c.deleted_at IS NULL AND (c.deleted_by <> ? OR c.deleted_by IS NULL) AND ((reciever_id IS NULL AND ur.sender_id IS NULL) OR (reciever_id<>c.sender_id AND ur.sender_id<>c.sender_id))', [$value['id'], $value['id'], $value['id'], $value['id'], $value['id']]);

        $res = $this->model->join('chat_reads AS cr', 'chat.id', '=', 'cr.chat_id')
            ->join('thread_users AS tu', 'chat.thread_id', '=', 'tu.thread_id')
            ->leftJoin('user_reports AS ur', function ($join) {
                $join->on('chat.sender_id', '=', 'ur.reciever_id');
                $join->on('ur.sender_id', '=', "?");
            })
        ->where('cr.type','<>','read_by_user')
        ->where('chat.sender_id','<>','?')
            ->where('tu.user_id','=','?')
            ->where('chat.deleted_at','=','NULL')*/
    }

    public function getThreadChat($thread_id)
    {
        return $this->model->select('chats.id', 'chats.thread_id', 'chats.sender_id', 'cr.user_id AS receiver_id', 'u.full_name AS username', 'chats.message', 'chats.created_at')
            ->selectRaw('(SELECT image FROM user_images WHERE user_id = chats.sender_id ORDER BY id DESC LIMIT 1) AS sender_image')
            //->selectRaw('(SELECT image FROM user_images WHERE user_id = cr.user_id ORDER BY id DESC LIMIT 1) AS receiver_image')
            ->from('chats')
            ->join('users AS u', 'chats.sender_id', '=', 'u.id')
            ->leftJoin('chat_reads AS cr', 'chats.id', '=', 'cr.chat_id')
            ->where('chats.thread_id', $thread_id)
            ->orderBy('chats.id', 'DESC')
            ->get();
    }

}
