<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Message $message */
        $message = $options['data'];

        $builder
            ->add('text', TextareaType::class, [
                'label' => '',
                'attr'  => [
                    'data-controller' => 'typing',
                    'data-typing-target-user-value' => $message->getIsTo()->getId()
                ]
            ])
            ->add('isFrom', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'attr'=> [
                    'class' => 'd-none'
                ],
                'data' => $message->getIsFrom()
            ])
            ->add('isTo', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'query_builder' => function (UserRepository $er) use ($message) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id != :excludedId')
                        ->setParameter('excludedId', $message->getIsFrom()->getId());
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
