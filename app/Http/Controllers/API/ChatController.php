<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\BlockUser;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Traits\apiresponse;
use App\Traits\bloackeduser;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Namu\WireChat\Events\MessageCreated;
use Namu\WireChat\Events\NotifyParticipant;

class ChatController extends Controller
{
    use apiresponse;
    use bloackeduser;

    public function getConversations()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Fetch all conversations of the authenticated user with participants and last messages
        $conversations = $user->conversations()->with([
            'participants' => function ($query) {
                $query->where('participantable_id', '!=', auth()->id())->select('participantable_type', 'participantable_id','conversation_id') // Exclude the authenticated user
                    ->with('participantable:id,name,avatar');
            },
            'lastMessage'
        ])->select('wire_conversations.id')->get();

        $conversations->transform(function ($conversation) {
            $isReadByAuth = $conversation->readBy($conversation->authParticipant ?? auth()->user()) || $conversation->id == request()->input('selectedConversationId');
            $conversation->readable = $isReadByAuth ? true : false;
            unset($conversation->authParticipant);
            unset($conversation->pivot);
            return $conversation;
        });

        // Return the conversations with the readable status
        return $this->success([
            'conversations' => $conversations,
        ], "Conversations fetched successfully", 200);
    }


    public function sendMessage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required_without:file|string',
            'file' => 'required_without:message|file|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422); // Use 422 for validation errors
        }

        DB::beginTransaction();
        try {
            // Blocked User Check // User blocked check
            if ($this->checkUserBlocked($request->to_user_id)) {
                return $this->error([], "This user is blocked.", 403);
            } elseif ($this->checkBlockedMe($request->to_user_id)) {
                return $this->error([], "This user has blocked you.", 403);
            }

            $auth = auth()->user();
            $recipient = User::where('id', $request->to_user_id)
                ->where('id', '!=', $auth->id) // Prevent sending messages to self
                ->where('status', 'active') // Ensure user is active
                ->first();
            if (!$recipient) {
                return $this->error([], 'Recipient not found', 404);
            }
            $sendMessage = $request->message;
            if ($request->hasFile('file') && $request->file('file')->isValid() && $request->message == null) {
                $rand = Str::random(6);
                $sendMessage = Helper::uploadImage($request->file('file'), 'message', "User-" . $auth->username . "-" . $rand . "-" . time());
            }
            // Use the sendMessageTo method from the Chatable trait
            $message = $auth->sendMessageTo($recipient, $sendMessage);

            // Broadcast events after successful message creation
            broadcast(new MessageCreated($message));
            broadcast(new NotifyParticipant($message->conversation->participant($recipient), $message));
            DB::commit();

            return $this->success(['message' => $message], "Message sent successfully", 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function groupCreate(Request $request)
    {
        // Step 1: Validate the input
        $validation = Validator::make($request->all(), [
            'name' => 'required|string', // Changed 'exists:users,id' to 'string' as 'name' is likely not user ID
            'description' => 'required_without:file|string',
            'photo' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048', // Made photo optional
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422); // Validation failed
        }

        // Step 2: Check if a user is authenticated
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User not authenticated', 401); // Return error if user is not authenticated
        }

        // Step 3: Handle the photo upload if it exists
        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = Helper::uploadImage($request->file('file'), 'message');
        }

        // Step 4: Create the group in a database transaction
        DB::beginTransaction();
        try {
            $conversation = $user->createGroup(
                name: $request->input('name'),
                description: $request->input('description'),
                photo: $photo
            );
            DB::commit();
            return $this->success($conversation, 'Group created successfully!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->error([], $th->getMessage(), 500);
        }
    }



    // public function groupMessageSend(Request $request, $chatId)
    // {
    //     // Validate the request
    //     $validated = $request->validate([
    //         'message' => 'required|string|max:1000',
    //     ]);

    //     // Ensure the user is part of the group chat
    //     $chat = Chat::findOrFail($chatId);

    //     // Check if the authenticated user is a participant of the group
    //     $isUserInChat = $chat->users()->where('user_id', Auth::id())->exists();
    //     if (!$isUserInChat) {
    //         return response()->json(['message' => 'You are not part of this chat.'], 403);
    //     }

    //     // Store the message in the database
    //     $message = Message::create([
    //         'chat_id' => $chatId,
    //         'sender_id' => Auth::id(),
    //         'message' => $validated['message'],
    //     ]);

    //     return response()->json(['message' => 'Message sent successfully!', 'message_data' => $message], 201);
    // }

    public function getUserConversation(User $user)
    {
        $otherUser = User::findOrFail($user->id);

        // Fetch the conversation with participants and messages
        $con = $otherUser->conversations()->with(
            'messages'
        )->select('wire_conversations.id')->first();

        $con->markAsRead();

        $messages = $con->messages->map(function ($message) {
            $message->isMe = $message->sendable_id == auth()->id();
            return $message;
        });
        $con->setRelation('messages', $messages);
        unset($con->pivot);
        return $this->success([
            'conversations' => $con,
            'youblocked' => $this->checkUserBlocked($user->id),
            'blockedyou' => $this->checkBlockedMe($user->id),
        ], "Conversations fetched successfully", 200);
    }



    public function createCovesation(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422);
        }

        $otherUser = User::find($request->user_id);
        if (!$otherUser) {
            return $this->error([], 'User Not found!', 422);
        }

        $auth = auth()->user();
        $conversation = $auth->createConversationWith($otherUser); // Pass the model, not ID

        return $this->success($conversation, 'Conversation Created Successfully', 200);
    }

    public function covesationBlock(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'blocked_user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422);
        }

        $authUser = auth()->user();

        if ($authUser->id == $request->blocked_user_id) {
            return $this->error([], 'You cannot block yourself.', 422);
        }

        $alreadyBlocked = BlockUser::where('user_id', $authUser->id)
            ->where('blocked_user_id', $request->blocked_user_id)
            ->first();

        if ($alreadyBlocked) {
            $alreadyBlocked->delete();
            return $this->success([], 'User unblocked',  200);
        }

        $block = BlockUser::create([
            'user_id' => $authUser->id,
            'blocked_user_id' => $request->blocked_user_id,
            'created_at' => now()
        ]);

        return $this->success($block, 'User blocked successfully.', 200);
    }



    public function searchUsers(Request $request)
    {
        // Validate the request to ensure 'query' exists
        $validation = Validator::make($request->all(), [
            'query' => 'required|string',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422); // Validation error
        }

        $searchQuery = $request->input('query');

        $user = new User();

        $users = $user->searchChatables($searchQuery);

        return $this->success([
            'users' => $users->select('id', 'name', 'avatar'),
        ], "Users fetched successfully", 200);
    }
}
