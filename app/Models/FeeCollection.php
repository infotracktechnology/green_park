<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeCollection extends Model
{
 

    public $table = 'fee_collection';

    protected $guarded = [];

    public function item()
    {
        return $this->hasMany(FeeCollectionItem::class,'collectionid','id');
    }
}