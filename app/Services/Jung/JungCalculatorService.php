<?php

namespace App\Services\Jung;

use App\Models\Jung\JungQuestion;
use App\Models\Jung\JungArchetype;
use App\Models\Jung\JungTestResult;
use App\Models\Jung\JungTestAnswer;
use Illuminate\Support\Facades\DB;

class JungCalculatorService
{
    /**
     * @param array $answers Format: [$questionId => $answerValue (1-5)]
     * @param int|null $userId
     * @return JungTestResult
     */
    public function calculate(array $answers, ?int $userId = null): JungTestResult
    {
        $questions = JungQuestion::with('archetype')->whereIn('id', array_keys($answers))->get();
        
        $archetypeScores = [];
        $detailedAnswers = [];
        $answerValues = [];

        // Initialize scores
        $archetypes = JungArchetype::where('is_active', true)->get();
        foreach ($archetypes as $archetype) {
            $archetypeScores[$archetype->id] = 0;
        }

        foreach ($questions as $question) {
            $value = (int) $answers[$question->id];
            $calculatedValue = $question->is_reverse ? (6 - $value) : $value;

            if (isset($archetypeScores[$question->archetype_id])) {
                $archetypeScores[$question->archetype_id] += $calculatedValue;
            }

            $answerValues[] = $value;

            $detailedAnswers[] = [
                'jung_question_id' => $question->id,
                'archetype_id' => $question->archetype_id,
                'answer_value' => $value,
                'calculated_value' => $calculatedValue,
                'is_reverse' => $question->is_reverse,
            ];
        }

        $percentages = $this->calculatePercentages($archetypeScores);
        $consistency = $this->detectConsistency($answerValues);

        // Sort archetypes to find primary, secondary, third
        // If tied, lower sort_order wins
        $sortedArchetypes = $archetypes->map(function ($arch) use ($archetypeScores) {
            $arch->score = $archetypeScores[$arch->id] ?? 0;
            return $arch;
        })->sort(function ($a, $b) {
            if ($a->score == $b->score) {
                return $a->sort_order <=> $b->sort_order;
            }
            return $b->score <=> $a->score; // Descending
        })->values();

        $primary = $sortedArchetypes->get(0);
        $secondary = $sortedArchetypes->get(1);
        $third = $sortedArchetypes->get(2);

        $generalEvaluation = $this->generateGeneralEvaluation($primary, $secondary, $third, $percentages);

        $result = DB::transaction(function () use ($userId, $primary, $secondary, $third, $archetypeScores, $percentages, $detailedAnswers, $consistency, $generalEvaluation) {
            
            $testResult = JungTestResult::create([
                'user_id' => $userId,
                'primary_archetype_id' => $primary ? $primary->id : null,
                'secondary_archetype_id' => $secondary ? $secondary->id : null,
                'third_archetype_id' => $third ? $third->id : null,
                'scores_json' => $archetypeScores,
                'percentages_json' => $percentages,
                'answers_json' => collect($detailedAnswers)->mapWithKeys(function($item) {
                    return [$item['jung_question_id'] => $item['answer_value']];
                })->toArray(),
                'consistency_json' => $consistency,
                'general_evaluation' => $generalEvaluation,
            ]);

            // Save detailed answers
            foreach ($detailedAnswers as $detail) {
                JungTestAnswer::create(array_merge($detail, [
                    'jung_test_result_id' => $testResult->id
                ]));
            }

            return $testResult;
        });

        return $result;
    }

    private function calculatePercentages(array $scores): array
    {
        $percentages = [];
        // Max score per archetype is 25 (5 questions * 5 points)
        foreach ($scores as $archId => $score) {
            $percentages[$archId] = round(($score / 25) * 100);
        }
        return $percentages;
    }

    private function detectConsistency(array $answerValues): array
    {
        $total = count($answerValues);
        if ($total == 0) return [];

        $sum = array_sum($answerValues);
        $average = $sum / $total;

        $counts = array_count_values($answerValues);
        $mostUsedCount = empty($counts) ? 0 : max($counts);
        $sameAnswerRatio = $mostUsedCount / $total;

        return [
            'average_answer' => round($average, 2),
            'same_answer_ratio' => round($sameAnswerRatio, 2),
            'positive_bias_warning' => $average > 4.7,
            'negative_bias_warning' => $average < 1.3,
            'low_discrimination_warning' => $sameAnswerRatio > 0.8,
        ];
    }

    private function generateGeneralEvaluation($primary, $secondary, $third, $percentages): string
    {
        if (!$primary || !$secondary || !$third) {
            return "Değerlendirme yapılamadı.";
        }

        $primaryTitle = $primary->title;
        $primaryDesc = $primary->description;
        $secondaryTitle = $secondary->title;
        $thirdTitle = $third->title;

        $text = "Baskın arketipiniz **{$primaryTitle}** olarak hesaplandı. Bu sonuç, {$primaryDesc} eğiliminizin diğer arketiplere göre daha belirgin olduğunu gösterir. İkinci sırada gelen **{$secondaryTitle}** arketipi profilinizi destekler. Üçüncü sıradaki **{$thirdTitle}** arketipi ise kişiliğinizde tamamlayıcı bir eğilim olarak görünür.";

        $primaryPct = $percentages[$primary->id] ?? 0;
        $secondaryPct = $percentages[$secondary->id] ?? 0;

        if (($primaryPct - $secondaryPct) <= 8) {
            $text .= "\n\n**Dikkat:** Birinci ve ikinci arketip arasındaki fark düşük olduğu için sonuç, tek bir arketipten çok iki güçlü eğilimin birleşimi olarak okunmalıdır.";
        }

        return $text;
    }
}
