<?php declare(strict_types=1);
namespace App\Form;

use App\Models\ShortcutAndUrl\ShortUrlRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShortUrlForm extends AbstractType
{
    const DESTINATION_URL_CHILD_NAME = 'destination_url';
    const SHORTCUT_CHILD_NAME = 'shortcut';
    const SUBMIT_CHILD_NAME = 'submit';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: self::DESTINATION_URL_CHILD_NAME,
                type: UrlType::class,
                options: [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'short_form.destination_url.placeholder',
                    ],
                    'default_protocol' => 'https'
                ]
            )
            ->add(
                child: self::SHORTCUT_CHILD_NAME,
                type: TextType::class,
                options: [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'short_form.shortcut.placeholder',
                    ]
                ]
            )
            ->add(
                child: self::SUBMIT_CHILD_NAME,
                type: SubmitType::class,
                options: [
                    'label' => 'short_form.submit.label'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShortUrlRequest::class
        ]);
    }
}