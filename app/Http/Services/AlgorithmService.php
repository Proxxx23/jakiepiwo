<?php
declare(strict_types=1);

namespace App\Http\Services;

use http\Env\Request;

class AlgorithmService
{
    /**
     * @param Request $request
     * @return string|null
     */
    protected function fetchJsonAnswers(Request $request): ?string
    {
        $answers = \json_decode($request->getQuery('json'));
        if (empty($answers)) {
            $this->logError('Brak odpowiedzi na pytania!');
            return null;
        }
        $questionsCount = \count(Questions::$questions);
        if ($questionsCount !== \count($answers)) {
            $this->logError('Liczba odpowiedni na pytania nie zgadza się z liczbą pytań!');
            return null;
        }

        for ($i = 1; $i <= $questionsCount; $i++) {
            if (null === $answers[$i]) {
                $this->logError('Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!');
            }
        }

        return $answers;
    }
}