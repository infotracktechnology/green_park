<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeCollectionItem extends Model
{
 

    public $table = 'feecollection_item';

    protected $guarded = [];

    public function instalment()
    {
        return $this->belongsTo(FeesPlanItem::class, 'feeid', 'id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentid', 'id');
    }


    public function feeplanitem()
    {
        return $this->belongsTo(FeesPlanItem::class, 'feeplan_item_id', 'id');
    }
}