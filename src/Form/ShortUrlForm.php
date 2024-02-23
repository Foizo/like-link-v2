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
                self::DESTINATION_URL_CHILD_NAME,
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'enter your https://..so.looong/..link',
                    ]
                ]
            )
            ->add(
                self::SHORTCUT_CHILD_NAME,
                TextType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'your-like-link (optional)',
                    ]
                ]
            )
            ->add(
                self::SUBMIT_CHILD_NAME,
                SubmitType::class,
                [
                    'label' => 'CREATE LIKELINK'
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