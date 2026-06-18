<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @property int $id
 * @property int $mass_message_id
 * @property int $user_id
 * @property string $status
 * @property int $attempts
 * @property ?string $last_error
 * @property MassMessage $massMessage
 * @property User $user
 * @property \DateTimeInterface $created_at
 * @property ?\DateTimeInterface $updated_at
 */
class MessageRecipient extends Model
{
    use HasFactory;


    protected $fillable = [
        'mass_message_id',
        'user_id',
        'status',
        'attempts',
        'last_error',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'mass_message_id' => 'integer',
            'user_id' => 'integer',
            'attempts' => 'integer',
        ];
    }


    public function massMessage(): BelongsTo
    {
        return $this->belongsTo(MassMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
