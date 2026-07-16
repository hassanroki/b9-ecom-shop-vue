<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_per_user',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $coupon) {
            $coupon->code = strtoupper($coupon->code);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function ($query) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {

                if (!$this->is_active) {
                    return 'Inactive';
                }

                if ($this->starts_at && now()->lt($this->starts_at)) {
                    return 'Scheduled';
                }

                if ($this->expires_at && now()->gt($this->expires_at)) {
                    return 'Expired';
                }

                return 'Active';
            }
        );
    }

    protected function formattedDiscount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === 'fixed'
                ? '৳' . $this->value
                : $this->value . '%'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expires_at
            ? now()->greaterThan($this->expires_at)
            : false;
    }

    public function hasStarted(): bool
    {
        return $this->starts_at
            ? now()->greaterThanOrEqualTo($this->starts_at)
            : true;
    }

    public function isUsable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->hasStarted()) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if (
            !is_null($this->usage_limit) &&
            $this->used_count >= $this->usage_limit
        ) {
            return false;
        }

        return true;
    }

    /**
     * Atomically increment the used count.
     * Use this instead of mass-assigning used_count.
     */
    public function recordUsage(): void
    {
        $this->increment('used_count');
    }

    protected $appends = ['status', 'formatted_discount'];
}
