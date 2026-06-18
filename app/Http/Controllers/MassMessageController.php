<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Models\MassMessage as MassMessageModel;
use App\Data\MassMessageData;
use App\Http\Requests\MassMessageRequest;
use App\Http\Requests\MassMessageIndexRequest;
use App\Actions\CreateMassMessageAction;
use App\Actions\GetMassMessagesAction;
use App\Actions\GetMassMessageStatusAction;
use Illuminate\Http\JsonResponse;
use App\Enums\MassMessageStatusEnum;

class MassMessageController extends Controller
{
    public function __construct(
        private readonly CreateMassMessageAction $createAction,
        private readonly GetMassMessagesAction $messagesAction,
        private readonly GetMassMessageStatusAction $statusAction,
    ) {}

    #[OA\Post(
        path: '/api/v1/mass-messages/send',
        description: 'Создает новую запись о массовой отправки SMS или Email-сообщений.',
        summary: 'Запустить массовую отправку сообщений',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['channel', 'priority', 'message', 'user_ids'],
                    properties: [
                        new OA\Property(
                            property: 'channel',
                            description: 'Канал связи для отправки сообщения (SMS или Email)',
                            type: 'string',
                            enum: ['sms', 'email']
                        ),
                        new OA\Property(
                            property: 'uuid',
                            description: 'UUID сообщения для определения дубликатов',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'priority',
                            description: 'Приоритет доставки: low=маркетинг, normal=обычные уведомления, high=транзакционные (обработка вне очереди)',
                            type: 'string',
                            enum: ['low', 'normal', 'high']
                        ),
                        new OA\Property(
                            property: 'message',
                            description: 'Текст сообщения для отправки',
                            type: 'string',
                            maxLength: 10000
                        ),
                        new OA\Property(
                            property: 'user_ids',
                            description: 'Массив идентификаторов получателей сообщения',
                            type: 'array',
                            items: new OA\Items(type: 'integer'),
                            minItems: 1
                        )
                    ],
                    type: 'object'
                )
            )
        ),
        tags: ['Mass Messages'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Массовая отправка успешно запущена',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'success', type: 'boolean'),
                            new OA\Property(
                                property: 'data',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'channel', type: 'string', enum: ['sms', 'email']),
                                    new OA\Property(property: 'priority', type: 'string', enum: ['low', 'normal', 'high'], description: 'Приоритет отправки'),
                                    new OA\Property(property: 'message_count', type: 'integer'),
                                    new OA\Property(
                                        property: 'status',
                                        type: 'string',
                                        enum: ['pending', 'processing', 'completed', 'failed']
                                    ),
                                    new OA\Property(
                                        property: 'created_at',
                                        type: 'string',
                                        format: 'date-time'
                                    )
                                ],
                                type: 'object'
                            )
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 400, description: 'Ошибка валидации входных данных'),
            new OA\Response(response: 422, description: 'Валидация не пройдена')
        ]
    )]
    public function send(MassMessageRequest $request): JsonResponse
    {
        $massMessageData = new MassMessageData(
            channel: $request->input('channel'),
            uuid: $request->input('uuid'),
            priority: $request->input('priority'),
            message: $request->input('message'),
            userIds: $request->input('user_ids')
        );

        $result = $this->createAction->handle($massMessageData);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }

    #[OA\Get(
        path: '/api/v1/mass-messages/{id}/status',
        description: 'Получает информацию о статусе запущенной массовой отправки.',
        summary: 'Получить статус массовой отправки',
        tags: ['Mass Messages'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID записи о массовой отправке',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Статус успешно получен',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'success', type: 'boolean'),
                            new OA\Property(
                                property: 'data',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'channel', type: 'string', enum: ['sms', 'email']),
                                    new OA\Property(property: 'priority', type: 'string', enum: ['low', 'normal', 'high'], description: 'Приоритет отправки'),
                                    new OA\Property(property: 'message', type: 'string'),
                                    new OA\Property(property: 'user_ids_count', type: 'integer'),
                                    new OA\Property(
                                        property: 'status',
                                        type: 'string',
                                        enum: ['pending', 'processing', 'completed', 'failed']
                                    ),
                                    new OA\Property(
                                        property: 'created_at',
                                        type: 'string',
                                        format: 'date-time'
                                    )
                                ],
                                type: 'object'
                            )
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 404, description: 'Запись не найдена')
        ]
    )]
    public function status(int $id): JsonResponse
    {
        $result = $this->statusAction->execute($id);

        if (!$result) {
            return response()->json(['error' => 'Запись не найдена'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    #[OA\Get(
        path: '/api/v1/mass-messages',
        description: 'Получает список всех запущенных массовых отправок с фильтрацией.',
        summary: 'Получить список массовых отправок',
        tags: ['Mass Messages'],
        parameters: [
            new OA\Parameter(
                name: 'channel',
                description: 'Фильтр по каналу связи (SMS или Email)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['sms', 'email'])
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Количество записей для возврата (макс. 50)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer', maximum: 50)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список успешно получен',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'success', type: 'boolean'),
                            new OA\Property(
                                property: 'data',
                                type: 'object',
                                properties: [
                                    new OA\Property(
                                        property: 'items',
                                        type: 'array',
                                        items: new OA\Items(
                                            type: 'object',
                                            properties: [
                                                new OA\Property(property: 'id', type: 'integer'),
                                                new OA\Property(property: 'channel', type: 'string', enum: ['sms', 'email']),
                                                new OA\Property(property: 'priority', type: 'string', enum: ['low', 'normal', 'high'], description: 'Приоритет отправки'),
                                                new OA\Property(
                                                    property: 'message_preview',
                                                    type: 'string',
                                                    maxLength: 100
                                                ),
                                                new OA\Property(property: 'user_ids_count', type: 'integer'),
                                                new OA\Property(
                                                    property: 'status',
                                                    type: 'string',
                                                    enum: ['pending', 'processing', 'completed', 'failed']
                                                ),
                                                new OA\Property(
                                                    property: 'created_at',
                                                    type: 'string',
                                                    format: 'date-time'
                                                )
                                            ]
                                        )
                                    ),
                                    new OA\Property(property: 'total', type: 'integer')
                                ]
                            )
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 400, description: 'Неверные параметры запроса')
        ]
    )]
    public function index(MassMessageIndexRequest $request): JsonResponse
    {
        $channel = $request->input('channel');
        $limit = (int) $request->input('limit', 50);

        $result = $this->messagesAction->execute($channel, $limit);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
