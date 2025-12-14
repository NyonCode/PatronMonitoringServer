<?php

namespace App\Models;

use App\Enums\TerminalLogDirection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TerminalLog extends Model
{
    protected $fillable = ['terminal_session_id', 'direction', 'content'];

    protected function casts(): array
    {
        return ['direction' => TerminalLogDirection::class];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TerminalSession::class, 'terminal_session_id');
    }
}
