<?php
// src/Controller/BookController.php
namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SellRepository;
use Symfony\Component\Validator\Constraints\DateTime as DateTimeConstraint;
use Symfony\Component\Validator\Validation;

class BookController extends AbstractController
{
    #[Route('/api/books/largest-checks', methods: ['GET'])]
    public function getBooksWithLargestChecks(Request $request, BookRepository $bookRepository, SellRepository $sellRepository): JsonResponse
    {
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $genreId = $request->query->get('genreId');
        $limit = (int) $request->query->get('limit', 5);

        if (!$from || !$to) {
            return $this->json(['error' => 'Missing required parameters'], 400);
        }

        $topSales = $bookRepository->findTopSales(
            new \DateTime($from),
            new \DateTime($to),
            $genreId ? (int) $genreId : null,
            $limit
        );

        // Вывод даты максимального чека, авторов и жанров
        $books = [];
        foreach($topSales as $topSale) {

            foreach($bookRepository->find($topSale['id'])->getAuthors() as $author) {
                $topSale['authors'][] = [
                    'id' => $author->getId(),
                    'name' => $author->getName(),
                ];
            }

            foreach($bookRepository->find($topSale['id'])->getGenres() as $genre) {
                $topSale['genres'][] = [
                    'id' => $genre->getId(),
                    'name' => $genre->getName(),
                ];
            }

            $topSale['last_sale_date'] = $sellRepository->largestPriceDate($topSale['id']);
            $books[] = $topSale;
        }

        return $this->json($books);
    }

    #[Route('/api/books/new', methods: ['POST'])]
    public function addBook(Request $request, AuthorRepository $authorRepository, GenreRepository $genreRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $year = intval($data['year']);
        $authors = explode(',', $data['authors']);
        $genres = explode(',', $data['genres']);

        $error = [];

        if (!$name || !$year || !$authors || !$genres) {
            $error[] = 'Missing required parameters';
        }

        $book = new Book();

        if(intval($year) != $year) {
            $error[] = 'Year is incorrect';
        }
        else {
            if($year > (int) date('Y')) {
                $error[] = 'Year must be less than current';
            }
        }

        foreach($authors as $author) {
            $a = $authorRepository->find($author);

            if(!empty($a)) {
                $book->addAuthor($a);
            }
            else {
                $error[] = 'Author with id ' . $author . ' not found';
            }
        }

        foreach($genres as $genre) {
            $g = $genreRepository->find($genre);

            if(!empty($g)) {
                $book->addGenre($g);
            }
            else {
                $error[] = 'Genre with id ' . $genre . ' not found';
            }
        }

        if(empty($error)) {

            $book->setName($name);
            $book->setPublicatedAt($year);

            $entityManager->persist($book);
            $entityManager->flush();

            return $this->json(['success' => true]);
        }
        else {

            return $this->json(['errors' => $error], 400);

        }
    }
}