<?php


namespace App\Normalizer;

use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class PaginationNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer)
    {
        
    }


    // methode qui explique cmt normaliser les choses 
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null 
    {
         if (!($object instanceof PaginationInterface)) {
            throw new \RuntimeException();
         }

         return [
            'items' => array_map(fn (Recipe $recipe) => $this->normalizer->normalize($recipe, $format, $context), $object->getItems()),
            'total' => $object->getTotalItemCount(),
            'page' => $object->getCurrentPageNumber(),
            'lastpage' => ceil($object->getTotalItemCount() / $object->getItemNumberPerPage())
         ];
    }

    //pr dire oui ou non il doit agir sur l'objet qu'on recoit 
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool 
    {
        return $data instanceof PaginatorInterface && $format === 'json';
    }

     //le normalizer ne peut ê declencher que lorsk j'ai qlq choz qui implemente la PaginationInterface 
    public function getSupportedTypes(?string $format): array 
    {
        return [
            PaginatorInterface::class => true 
        ];
    }
}
