<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $message_recipient_id
 * @property string $provider
 * @property string $status
 * @property string|null $response_code
 * @property string|null $response_body
 * @property string|null $error_message
 * @property MessageRecipient $messageRecipient
 * @property \DateTimeInterface $created_at
 * @property ?\DateTimeInterface $updated_at
 */
class MessageDeliveryLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'message_recipient_id',
        'provider',
        'status',
        'response_code',
        'response_body',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'message_recipient_id' => 'integer',
            'response_code' => 'string',
        ];
    }

    public function messageRecipient(): BelongsTo
    {
        return $this->belongsTo(MessageRecipient::class);
    }
}
