<?php
namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Event;
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
    
        for ($i = 0; $i < 10; $i++) {
            $author = new Author();
            $author->setName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setBirthday($this->faker->dateTime())
                ->setBiography($this->faker->text());
    
            $authorList[] = $author;
            $manager->persist($author);
        }
    
        $manager->flush();
    }    

    //`php bin/console doctrine:fixtures:load ` Execute tes Fixtures.
}