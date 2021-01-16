<?php
namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SearchType extends AbstractType
{
    // à l'aide de la fonction buildFiorm on pourra creer notre formulaire avec 3 entrées string, categories, sumbit
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string', TextType::class, [
                "label" => 'Rechercher',
                "required" => false,
                "attr" => [
                    'placeholder' => 'Votre recherche...',
                    'class'=>'form-control-sm'
                ]
            ])
            ->add('categories', EntityType::class,[
                'label'=>false,
                'required'=>false,
                'class'=> Category::class,
                'multiple'=>true,
                'expanded'=>true,

            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Filtrer',
                'attr'=>[
                    'class'=>'btn-block btn-info'
                ]
            ])
        ;
    }
    // on fait la liason entre le formulaire et mon objet Search à l'aide de configureOptions.
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'methode'=> 'GET',
            'crsf_protection'=> false,
        ]);
    }
    public function getBlockPrefix()
    {
        return  '';
    }
}
