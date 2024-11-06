<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    #[Route('/api/genre/{idGenre}', name:  'genre.get', methods: ['GET'])]
    #[ParamConverter("genre", options: ["id" => "idGenre"])]
    
   public function getGenre(Genre $genre, SerializerInterface $serializer): JsonResponse 
   {
       $jsonGenre = $serializer->serialize($genre, 'json', ["groups" => "getAllGenres"]);
       return new JsonResponse($jsonGenre, Response::HTTP_OK, ['accept' => 'json'], true);
   }

   /**
     * Create new genre
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/genre', name: 'genre.post', methods: ['POST'])]
    public function createGenre(Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, TagAwareCacheInterface $cache): JsonResponse{
        $genre = $serializer->deserialize($request->getContent(), Genre::class,'json');  
        
        $errors = $validator->validate($genre);
        if($errors ->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        
        $entityManager->persist($genre);
        $entityManager->flush();
        $cache->invalidateTags(["authorCache"]);

        $jsonGenre= $serializer->serialize($genre,'json');

        $location = $urlGenerator->generate('genre.get', ['idGenre'=> $genre->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonGenre,Response::HTTP_CREATED,["Location" => $location],true);

    }

    /** 
     * Update genre with a id
     *
     * @param Genre $genre
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/genre/{id}', name: 'genre.update', methods: ['PUT'])]
    public function updateGenre(Genre $genre, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

        $updatedGenre = $serializer->deserialize($request->getContent(), Genre::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>$genre]);
        $entityManager->persist($updatedGenre);
        $entityManager->flush();
        $cache->invalidateTags(["genreCache"]);
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }
}
