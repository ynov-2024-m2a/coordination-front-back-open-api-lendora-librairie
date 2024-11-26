<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
//use App\Repository\EventRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        SerializerInterface $serializer,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        //$authors = $repository->findAll();
        $idCache = "getAllAuthors";
        $jsonAuthors = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer) {
            $item->tag("authorsCache");
            $authorsList = $repository->findAll();
            return $serializer->serialize($authorsList, 'json', ['groups' => 'getAllAuthors']);
        });
        //$jsonAuthors = $serializer->serialize($authors, 'json', ["groups" => "getAllAuthors"]);
        $response = new JsonResponse($jsonAuthors, Response::HTTP_OK, [], true);
        $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=3600');
        return $response;
    }

    /**
    * Return author with id
    *
    * @param Author $author
    * @param SerializerInterface $serializer
    * @return JsonResponse
    */
    #[Route('/api/authors/{idAuthor}', name:  'author.get', methods: ['GET'])]
    #[ParamConverter("author", options: ["id" => "idAuthor"])]
    public function getAuthor(Author $author, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {

        $jsonBook = $serializer->serialize(
            $author,
            'jsonld', // Spécifiez le format JSON-LD
            ['groups' => ['getAllAuthors']] // Utilisez vos groupes de sérialisation
        );

        return new JsonResponse($jsonBook, JsonResponse::HTTP_OK, ['Content-Type' => 'application/ld+json'], true);
    }
    /**
     * Create new author
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     */
    #[Route('/api/authors', name: 'author.post', methods: ['POST'])]
    public function createAuthor(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        // Désérialisation au format JSON-LD
        $author = $serializer->deserialize($request->getContent(), Author::class, 'jsonld');

        // Validation des données
        $errors = $validator->validate($author);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'jsonld'),
                JsonResponse::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/ld+json'],
                true
            );
        }

        // Sauvegarde de l'entité
        $entityManager->persist($author);
        $entityManager->flush();
        $cache->invalidateTags(["authorsCache"]);

        // Génération de la réponse JSON-LD
        $jsonAuthor = $serializer->serialize($author, 'jsonld', ['groups' => ['getAllAuthors']]);

        $location = $urlGenerator->generate(
            'author.get',
            ['idAuthor' => $author->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse(
            $jsonAuthor,
            Response::HTTP_CREATED,
            ["Location" => $location, 'Content-Type' => 'application/ld+json'],
            true
        );
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

        $cache->invalidateTags(["authorsCache"]);

        return new JsonResponse($serializer->serialize($updatedAuthor, 'json', ['groups' => 'getAllAuthors']), Response::HTTP_OK, [], true);
    }

    /**
     * Delete author with a id
     *
     * @param Author $books
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/authors/{id}', name: 'author.delete', methods: ['DELETE'])]
    public function deleteAuthor(Author $author, Request $request, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

        $entityManager->remove($author);

        $entityManager->flush();
        $cache->invalidateTags(["authorsCache"]);
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }

}
