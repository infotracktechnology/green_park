<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubjectReport extends Model
{
    protected $table = 'examsubjectreport';
    protected $guarded = [];

    private const SUBJECT_MAP = [
        'PHYSICS'   => ['tot' => 'ptot', 'r' => 'pr', 'w' => 'pw', 'l' => 'pl'],
        'CHEMISTRY' => ['tot' => 'ctot', 'r' => 'cr', 'w' => 'cw', 'l' => 'cl'],
        'BOTANY'    => ['tot' => 'btot', 'r' => 'br', 'w' => 'bw', 'l' => 'bl'],
        'ZOOLOGY'   => ['tot' => 'ztot', 'r' => 'zr', 'w' => 'zw', 'l' => 'zl'],
    ];

    public function Header($ctype)
    {

        return match (true) {
            $this->isGrandTest($ctype)        => ['PHYSICS', 'CHEMISTRY', 'BOTANY', 'ZOOLOGY'],
            $this->isCumulativeChebot($ctype) => ['CHEMISTRY', 'BOTANY'],
            $this->isCumulativePhyzoo($ctype) => ['PHYSICS', 'ZOOLOGY'],
            $this->isWeekendTest($ctype)      => $this->extractWeekendSubject($ctype),
            default                            => [],
        };
    }

    public function getScoresForHeader($ctype)
    {
        $subjects = $this->header($ctype);
        $scores = [];

        if ($this->isWeekendTest(trim($ctype ?? ''))) {
            $total = ($this->r + $this->w + $this->l) * 4;
            $scores[$subjects[0] ?? 'UNKNOWN'] = [$this->tot, $total];
            $scores['TOTAL'] = [$this->tot, $total];
            return $scores;
        }

        $subjectScores = collect($subjects)->mapWithKeys(function ($subject) {
            $fields = self::SUBJECT_MAP[$subject];
            $total = ($this->{$fields['r']} + $this->{$fields['w']} + $this->{$fields['l']}) * 4;
            return [$subject => [$this->{$fields['tot']}, $total]];
        });

        $totalObtained = $subjectScores->sum(fn($v) => $v[0]);
        $totalPossible = $subjectScores->sum(fn($v) => $v[1]);
        $subjectScores['TOTAL'] = [$totalObtained, $totalPossible];

        return $subjectScores->toArray();
    }

    private function extractWeekendSubject(string $ctype)
    {
        if (preg_match('/\(([^)]+)\)/', $ctype, $matches)) {
            return [$matches[1]];
        }
        return [];
    }

    private function isGrandTest(string $ctype): bool
    {
        return str_starts_with($ctype, 'GRAND TEST');
    }

    private function isCumulativeChebot(string $ctype): bool
    {
        return str_starts_with($ctype, 'CUMULATIVE') && str_contains($ctype, 'CHEBOT');
    }

    private function isCumulativePhyzoo(string $ctype): bool
    {
        return str_starts_with($ctype, 'CUMULATIVE') && str_contains($ctype, 'PHYZOO');
    }

    private function isWeekendTest(string $ctype): bool
    {
        return str_starts_with($ctype, 'WEEKEND');
    }
}
