<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'domain',
        'domain_started_at',
        'domain_expires_at',
        'ssl_started_at',
        'ssl_expires_at',
        'address',
        'additional_address',
        'phone',
        'fax',
        'additional_contact',
        'mobile',
        'instagram',
        'facebook',
        'whatsapp',
        'google_analytics_code',
        'coordinates',
        'modules',
        'image_sizes',
        'api_token',
        'auto_translate',
    ];

    protected $casts = [
        'modules'           => 'array',
        'image_sizes'       => 'array',
        'domain_started_at' => 'date',
        'domain_expires_at' => 'date',
        'ssl_started_at'    => 'date',
        'ssl_expires_at'    => 'date',
    ];

    public function domainRemainingDays(): ?int
    {
        if (!$this->domain_expires_at) return null;
        return (int) now()->startOfDay()->diffInDays($this->domain_expires_at, false);
    }

    public function sslRemainingDays(): ?int
    {
        if (!$this->ssl_expires_at) return null;
        return (int) now()->startOfDay()->diffInDays($this->ssl_expires_at, false);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($client) {
            if (empty($client->api_token)) {
                $client->api_token = bin2hex(random_bytes(30));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function languages()
    {
        return $this->hasMany(ClientLanguage::class);
    }

    public function hasModule(string $module): bool
    {
        if (is_null($this->modules)) {
            return false;
        }
        return in_array($module, $this->modules);
    }

    public function getImageSize(string $module): ?array
    {
        if (is_null($this->image_sizes) || !isset($this->image_sizes[$module])) {
            return null;
        }
        $sizeStr = $this->image_sizes[$module];
        if (empty($sizeStr)) {
            return null;
        }
        $parts = explode('x', strtolower($sizeStr));
        if (count($parts) !== 2) {
            return null;
        }
        $width = (int)trim($parts[0]);
        $height = (int)trim($parts[1]);
        if ($width <= 0 || $height <= 0) {
            return null;
        }
        return [
            'width' => $width,
            'height' => $height
        ];
    }
}
