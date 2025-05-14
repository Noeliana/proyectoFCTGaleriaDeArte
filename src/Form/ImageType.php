<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Subcategory;
use App\Entity\Tag;
use App\Repository\ArtistRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    private $security;
    private $artistRepository;

    public function __construct(Security $security, ArtistRepository $artistRepository)
    {
        $this->security = $security;
        $this->artistRepository = $artistRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $artists = $isAdmin
            ? $this->artistRepository->findAll()
            : $this->artistRepository->findBy(['user' => $user]);

        $builder
            ->add('title')
            ->add('description')
            ->add('imageFile', FileType::class, [
                'label' => 'Sube tu imagen',
                'mapped' => false,
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('artist', EntityType::class, [
                'class' => Artist::class,
                'choices' => $artists,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Selecciona un artista',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('subCategory', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'label' => 'Subcategoría',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
