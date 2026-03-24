<?php

namespace Modules\Shopify\Models;

use App\Http\Traits\HasUuid;
use App\Models\Contact;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopifyNotificationLog extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
    ];

    public function shopifyStore()
    {
        return $this->belongsTo(ShopifyStore::class, 'shopify_store_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
}
