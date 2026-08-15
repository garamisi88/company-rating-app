<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Email;

final class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'review.form.company_name',
                'attr' => [
                    'maxLength' => 255,
                    'autofocus' => true,
                ],
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'review.form.rating',
                'choices' => array_combine(range(1, 5), range(1, 5)),
                'choice_translation_domain' => false,
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
            ])
            ->add('reviewText', TextareaType::class, [
                'label' => 'review.form.review_text',
                'attr' => ['rows' => 6],
            ])
            ->add('authorEmail', EmailType::class, [
                'label' => 'review.form.author_email',
                'constraints' => [new Email()],
            ])
            ->add('website', TextType::class, [
                'label' => 'review.form.website',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Blank()],
                'attr' => ['autocomplete' => 'off', 'tabindex' => '-1'],
                'row_attr' => ['class' => 'honeypot', 'aria-hidden' => 'true'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
