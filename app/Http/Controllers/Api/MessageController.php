<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Models\Dialog;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    /**
     * @var MessageService
     */
    private $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * @OA\Post(
     *     path="/api/chat/{chatId}/send/{userId}",
     *     summary="Send user to chat by ID",
     *     description="Send user to chat by ID",
     *     operationId="chatSendMessage",
     *     tags={"Chat"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of caht",
     *         in="path",
     *         name="chatId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User info",
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", format="text", example="message")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's password updated response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param CreateMessageRequest $request
     * @param int $chatId
     * @param int $userId
     * @return JsonResponse
     */
    public function postMessage(CreateMessageRequest $request, int $chatId, int $userId){
        return $this->messageService->createMessage($chatId, $userId, $request);
    }

    /**
     * @OA\Get(
     *     path="/api/chat/{chatId}",
     *     summary="Get chat messages",
     *     description="Get chat messages",
     *     operationId="chatMessages",
     *     tags={"Chat"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="Chat ID",
     *         in="path",
     *         name="chatId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $chatId
     * @return JsonResponse
     */
    public function getMessages(int $chatId){
        return $this->messageService->getMessages($chatId);
    }

    /**
     * @OA\Delete(
     *     path="/api/chat/{chatId}/message/{messageId}",
     *     summary="Delete message by ID",
     *     description="Delete message by ID",
     *     operationId="chatDeleteMessage",
     *     tags={"Chat"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="Chat ID",
     *         in="path",
     *         name="chatId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Message ID",
     *         in="path",
     *         name="messageId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $chatId
     * @param int $messageId
     * @return JsonResponse
     */
    public function deleteMessage(int $chatId, int $messageId){
        return $this->messageService->deleteMessage($messageId);
    }

    /**
     * @OA\Post(
     *     path="/api/chat/create/{userId}/with/{friendId}",
     *     summary="Send user to chat by ID",
     *     description="Send user to chat by ID",
     *     operationId="chatSendMessage",
     *     tags={"Chat"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="ID of friend",
     *         in="path",
     *         name="friendId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's password updated response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $userId
     * @param int $friendId
     * @return JsonResponse
     */
    public function createChat(int $userId, int $friendId){
        return $this->messageService->createChat($userId, $friendId);
    }

}
