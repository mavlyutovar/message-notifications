<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $channel
 * @property string $priority
 * @property string $message
 * @property string $status
 * @property string $idempotency_key
 * @property MessageRecipient[] $recipients
 * @property \DateTimeInterface $created_at
 * @property ?\DateTimeInterface $updated_at
 */
class MassMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'priority',
        'message',
        'status',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class);
    }
}
