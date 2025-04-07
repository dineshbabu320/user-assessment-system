<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $questions = [
            [
                'exam_id' => 4, // Replace with the actual exam ID
                'question_text' => 'What is 2 + 2?',
                'question_type' => 'single',
                'answers' => [
                    ['answer_text' => '3', 'is_correct' => false],
                    ['answer_text' => '4', 'is_correct' => true],
                    ['answer_text' => '5', 'is_correct' => false],
                ],
            ],
            [
                'exam_id' => 4, // Replace with the actual exam ID
                'question_text' => 'Which of the following are prime numbers?',
                'question_type' => 'mcq',
                'answers' => [
                    ['answer_text' => '2', 'is_correct' => true],
                    ['answer_text' => '3', 'is_correct' => true],
                    ['answer_text' => '4', 'is_correct' => false],
                ],
            ],
            [
                'exam_id' => 4, // Replace with the actual exam ID
                'question_text' => 'What is the capital of France?',
                'question_type' => 'single',
                'answers' => [
                    ['answer_text' => 'Berlin', 'is_correct' => false],
                    ['answer_text' => 'Paris', 'is_correct' => true],
                    ['answer_text' => 'Madrid', 'is_correct' => false],
                ],
            ],
            [
                'exam_id' => 4, // Replace with the actual exam ID
                'question_text' => 'Write a short note on climate change.',
                'question_type' => 'text',
                'answers' => [], // No predefined answers for text-based questions
            ],
            [
                'exam_id' => 4, // Replace with the actual exam ID
                'question_text' => 'Which of the following are programming languages?',
                'question_type' => 'mcq',
                'answers' => [
                    ['answer_text' => 'Python', 'is_correct' => true],
                    ['answer_text' => 'HTML', 'is_correct' => false],
                    ['answer_text' => 'Java', 'is_correct' => true],
                ],
            ],
        ];

        foreach ($questions as $q) {
            // Create the question
            $question = Question::create([
                'exam_id' => $q['exam_id'],
                'question_text' => $q['question_text'],
                'question_type' => $q['question_type'],
                'options' => $q['question_type'] === 'mcq' ? json_encode(array_column($q['answers'], 'answer_text')) : null,
            ]);

            // Create the answers
            foreach ($q['answers'] as $a) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $a['answer_text'],
                    'is_correct' => $a['is_correct'],
                ]);
            }
        }
    }
}
