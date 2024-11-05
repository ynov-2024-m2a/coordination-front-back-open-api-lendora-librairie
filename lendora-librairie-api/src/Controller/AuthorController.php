<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\EventRepository;

use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use OpenApi\Annotations as OA;

class AuthorController extends AbstractController
{
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthorController.php',
        ]);
    }

    /**
     * Renvoie tous les auteurs
     *
     * @param AuthorRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     **/
    #[Route('/api/authors', name: 'author.getAll', methods:['GET'])]
    public function getAllAuthors(
        AuthorRepository $repository,
        SerializerInterface $serializer
    ): JsonResponse {
        $authors = $repository->findAll();
        $jsonAuthors = $serializer->serialize($authors, 'json', ["groups" => "getAllAuthors"]);
        return new JsonResponse($jsonAuthors, Response::HTTP_OK, [], true);
    }

        /**
        * Renvoie un author par son id
        *
        * @param Author $author
        * @param SerializerInterface $serializer
        * @return JsonResponse
    */
    #[Route('/api/author/{idAuthor}', name:  'author.get', methods: ['GET'])]
    #[ParamConverter("author", options: ["id" => "idAuthor"])]
    
   public function getAuthor(Author $author, SerializerInterface $serializer): JsonResponse 
   {
       $jsonBooks = $serializer->serialize($book, 'json', ["groups" => "getAllBooks"]);
       return new JsonResponse($jsonBooks, Response::HTTP_OK, ['accept' => 'json'], true);
   }
}
