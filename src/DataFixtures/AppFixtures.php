<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Sell;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Добавление жанров
        $genres = [];
        $genres_types = [
            'Фэнтези',
            'Альтернативная история',
            'Боевая фантастика',
            'Постапокалипсис',
            'Публицистика',
        ];

        foreach($genres_types as $elem) {

            $genre = new Genre();
            $genre->setName($elem);
            $manager->persist($genre);
            $manager->flush();
            $genres[] = $genre;

        }

        // Добавление авторов
        $authors = [];
        $authors_data = [
            [
                'name' => 'Стивен Кинг',
                'birth_date' => new \DateTime('1947-09-21'),
            ],
            [
                'name' => 'Аабов Ааб',
                'birth_date' => new \DateTime('1948-09-21'),
            ],
            [
                'name' => 'Аалай Мухаммед',
                'birth_date' => new \DateTime('1949-09-21'),
            ],
            [
                'name' => 'Абакумов Олег Юрьевич',
                'birth_date' => new \DateTime('1950-09-21'),
            ],
            [
                'name' => 'Абакумов Олег Александрович',
                'birth_date' => new \DateTime('1951-09-21'),
            ],
        ];

        foreach($authors_data as $elem) {

            $author = new Author();
            $author->setName($elem['name']);
            $author->setBirthDate($elem['birth_date']);
            $manager->persist($author);
            $manager->flush();
            $authors[] = $author;

        }

        // Добавление книг
        $books = [];

        for($i = 0; $i < 10; $i++) {

            $book = new Book();
            $book->setName('Книга '.$i);
            $book->setPublicatedAt(random_int(1990, 2025));

            // Привязка авторов к книге
            $author_count = random_int(1, count($authors) - 1);
            for($j = 0; $j < $author_count; $j++) {
                $book->addAuthor($authors[random_int(0, count($authors) - 1)]);
            }

            // Привязка жанров к книге
            $genre_count = random_int(1, count($genres) - 1);
            for($j = 0; $j < $genre_count; $j++) {
                $book->addGenre($genres[random_int(0, count($genres) - 1)]);
            }

            $manager->persist($book);
            $manager->flush();
            $books[] = $book;
        }

        // Добавление продаж
        $sells_count = random_int(1, 15);
        for($i = 0; $i < $sells_count; $i++) {

            $sell = new Sell();
            $sell->setBook($books[random_int(0, count($books) - 1)]);
            $sell->setCount(random_int(1, 15));
            $sell->setPricePerUnit(random_int(500, 2500));
            $sell->setCreatedAt(new \DateTime());
            $manager->persist($sell);
            $manager->flush();

        }
    }
}
