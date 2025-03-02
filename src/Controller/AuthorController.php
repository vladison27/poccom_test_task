<?php
// src/Controller/AuthorController.php
namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SellRepository;
use Symfony\Component\Validator\Constraints\DateTime as DateTimeConstraint;
use Symfony\Component\Validator\Validation;

class AuthorController extends AbstractController
{
    #[Route('/api/authors/popular', methods: ['GET'])]
    public function getPopularAuthors(Request $request, SellRepository $sellRepository): JsonResponse
    {
        // Получение параметров
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $genreId = $request->query->get('genreId');
        $limit = (int) $request->query->get('limit', 5);

        // Проверка полей
        if (!$from || !$to) {
            return new JsonResponse(['error' => 'Missing required parameters'], 400);
        }

        // Валидация формата дат
        $validator = Validation::createValidator();
        $dateFormat = 'd.m.Y H:i:s';

        $errors = $validator->validate($from, new DateTimeConstraint(['format' => $dateFormat]));
        $errors->addAll($validator->validate($to, new DateTimeConstraint(['format' => $dateFormat])));

        if (count($errors) > 0) {
            return new JsonResponse(['error' => 'Invalid date format'], 400);
        }

        // Преобразование дат
        $fromDate = \DateTime::createFromFormat($dateFormat, $from);
        $toDate = \DateTime::createFromFormat($dateFormat, $to);
        $toDate->setTime(23, 59, 59);

        // Получение данных
        $result = $sellRepository->findPopularAuthors(
            $fromDate,
            $toDate,
            $genreId ? (int)$genreId : null,
            max($limit, 1)
        );

        // Форматирование ответа
        $responseData = [];
        foreach ($result as $item) {
            $responseData[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'birth_date' => $item['birth_date']->format('d.m.Y'),
                'total_sold' => (int)$item['totalSold']
            ];
        }

        return new JsonResponse($responseData);
    }
}