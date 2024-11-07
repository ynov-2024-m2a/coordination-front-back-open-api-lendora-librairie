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
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * Return authors
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
        * Return author with id
        *
        * @param Author $author
        * @param SerializerInterface $serializer
        * @return JsonResponse
    */
    #[Route('/api/author/{idAuthor}', name:  'author.get', methods: ['GET'])]
    #[ParamConverter("author", options: ["id" => "idAuthor"])]
    
   public function getAuthor(Author $author, SerializerInterface $serializer): JsonResponse 
   {
       $jsonAuthor = $serializer->serialize($author, 'json', ["groups" => "getAllAuthors"]);
       return new JsonResponse($jsonAuthor, Response::HTTP_OK, ['accept' => 'json'], true);
   }

    /**
     * Create new author
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/author', name: 'author.post', methods: ['POST'])]
    public function createAuthor(Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, TagAwareCacheInterface $cache): JsonResponse{
        $author = $serializer->deserialize($request->getContent(), Author::class,'json');  
        
        $errors = $validator->validate($author);
        if($errors ->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        
        $entityManager->persist($author);
        $entityManager->flush();
        $cache->invalidateTags(["authorCache"]);

        $jsonAuthor= $serializer->serialize($author,'json');

        $location = $urlGenerator->generate('author.get', ['idAuthor'=> $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonAuthor,Response::HTTP_CREATED,["Location" => $location],true);

    }

    /** 
     * Update author with a id
     *
     * @param Author $author
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/authors/{id}', name: 'author.update', methods: ['PUT'])]
    public function updateAuthor(Author $author, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse
    {
        $updatedAuthor = $serializer->deserialize($request->getContent(), Author::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $author]);

        $entityManager->persist($updatedAuthor);
        $entityManager->flush();

        $cache->invalidateTags(["authorCache"]);

        return new JsonResponse($serializer->serialize($updatedAuthor, 'json', ['groups' => 'getAllAuthors']), Response::HTTP_OK, [], true);
    }

}
