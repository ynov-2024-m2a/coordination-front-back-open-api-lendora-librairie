<?php
namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Event;
use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $authorList = [];
        $genreList = [];

        // Créer des auteurs
        for ($i = 0; $i < 10; $i++) {
            $author = new Author();
            $author->setName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setBirthday($this->faker->dateTime())
                ->setBiography($this->faker->text());

            $authorList[] = $author;
            $manager->persist($author);
        }

        // Créer des genres
        for ($j = 0; $j < 10; $j++) {
            $genre = new Genre();
            $genre->setName($this->faker->word());

            $genreList[] = $genre;
            $manager->persist($genre);
        }

        // Créer des livres et les relier aux auteurs et genres
        for ($k = 0; $k < 20; $k++) {
            $book = new Book();
            $book->setTitle($this->faker->sentence(3))
                ->setReleaseDate($this->faker->dateTime())
                ->setBlurb($this->faker->text());

            // Associer un auteur au livre
            $randomAuthor = $authorList[array_rand($authorList)];
            $book->setAuthor($randomAuthor);
            $randomAuthor->addBook($book);

            // Associer des genres aléatoires au livre
            $randomGenres = $this->faker->randomElements($genreList, rand(1, 3));
            foreach ($randomGenres as $genre) {
                $book->addGenre($genre);
                $genre->addBook($book);
            }

            $manager->persist($book);
        }

        $manager->flush();
    }


    //`php bin/console doctrine:fixtures:load ` Execute tes Fixtures.
}