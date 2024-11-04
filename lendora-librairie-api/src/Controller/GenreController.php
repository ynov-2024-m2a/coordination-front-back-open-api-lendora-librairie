<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenreController extends AbstractController
{
    #[Route('/genre', name: 'app_genre')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/GenreController.php',
        ]);
    }

    /**
     * Renvoie tous les genres
        *
        * @param GenreRepository $repository
        * @param SerializerInterface $serializer
        * @return JsonResponse
     */
    #[Route('/api/genres', name: 'genre.getAll', methods:['GET'])]
    public function getAllGenres(
        GenreRepository $repository,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $genres =  $repository->findAll();
        $jsonGenre = $serializer->serialize($genres, 'json',["groups" => "getAllGenres"]);
        return new JsonResponse(    
            $jsonGenre,
            Response::HTTP_OK, 
            [], 
            true
        );
    } 

    /**
        * Renvoie un book par son id
        *
        * @param Genre $genre
        * @param SerializerInterface $serializer
        * @return JsonResponse
    */
    #[Route('/api/genres/{idGenre}', name:  'genre.get', methods: ['GET'])]
    #[ParamConverter("genre", options: ["id" => "idGenre"])]
    
   public function getGenre(Genre $genre, SerializerInterface $serializer): JsonResponse 
   {
       $jsonGenre = $serializer->serialize($genre, 'json', ["groups" => "getAllGenres"]);
       return new JsonResponse($jsonGenre, Response::HTTP_OK, ['accept' => 'json'], true);
   }
}
