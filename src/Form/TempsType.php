<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\TempsProduction;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TempsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projet', EntityType::class, [
                'label' => 'Projet concerné',
                'class' => Projet::class,
                'choice_label' => 'nom',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.date_livraison IS NULL');
                 }
            ])
            ->add('heuresTravailles', IntegerType::class, [
                'label' => 'Nombre d\'heures travaillées',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TempsProduction::class,
        ]);
    }
}
