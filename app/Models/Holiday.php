<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    use HasFactory;
    protected $table = 'holiday';
    protected $guarded = [];

    function branch()
    {
        if(strpos($this->branch_id, ',')){
            return Branch::whereIn('id', explode(',', $this->branch_id))->get()->implode('name', ', ');
        }
        return Branch::find($this->branch_id)->name ?? '';
    }

    public static function isHoliday($date = '', $branch_id = null, $hostel = null, $gender = null, $section = null)
    {
        if (empty($date)) {
            $datetime = Carbon::now()->format('Y-m-d H:i:s');
        } elseif (strlen(trim($date)) <= 10) {
            // Date mattum pass panna ('2026-11-24'), current time-oda check pannum
            $datetime = trim($date) . ' ' . date('H:i:s');
        } else {
            // Already datetime pass panna, double time append aagathu
            $datetime = Carbon::parse($date)->format('Y-m-d H:i:s');
        }

        // 1. Check "Week Of" holiday (branch, hostel, gender, section check pannum)
        $week_of = self::where('type', 'Week Of')
            ->where('start_date', '<=', $datetime)
            ->where('end_date', '>=', $datetime)
            ->when($branch_id, function ($q) use ($branch_id) {
                $q->whereRaw('FIND_IN_SET(?, branch_id)', [$branch_id]);
            })
            ->when($hostel, function ($q) use ($hostel) {
                $q->whereRaw('FIND_IN_SET(?, hostel)', [$hostel]);
            })
            ->when($gender, function ($q) use ($gender) {
                $q->whereRaw('FIND_IN_SET(?, gender)', [$gender]);
            })
            ->when($section, function ($q) use ($section) {
                $q->where(function ($sub) use ($section) {
                    $sub->whereRaw('FIND_IN_SET(?, section)', [$section])
                        ->orWhereRaw("FIND_IN_SET('ALL', section)");
                });
            })
            ->exists();

        if ($week_of) {
            return true;
        }

        // 2. Check Normal Holiday (type != 'Week Of', branch & hostel check pannum)
        $leave = self::where(function ($q) {
                $q->where('type', '!=', 'Week Of')->orWhereNull('type');
            })
            ->where('start_date', '<=', $datetime)
            ->where('end_date', '>=', $datetime)
            ->when($branch_id, function ($q) use ($branch_id) {
                $q->whereRaw('FIND_IN_SET(?, branch_id)', [$branch_id]);
            })
            ->when($hostel, function ($q) use ($hostel) {
                $q->whereRaw('FIND_IN_SET(?, hostel)', [$hostel]);
            })
            ->exists();

        return $leave;
    }
}
