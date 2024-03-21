<?php declare(strict_types=1);
namespace App\Form;

use App\Models\Contact\ContactRequest;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactForm extends AbstractType
{
    const NAME_CHILD_NAME = 'name';
    const EMAIL_CHILD_NAME = 'email';
    const SUBJECT_CHILD_NAME = 'subject';
    const MESSAGE_CHILD_NAME = 'message';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: self::NAME_CHILD_NAME,
                type: TextType::class,
                options: [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'contact_form.name.placeholder',
                    ]
                ]
            )
            ->add(
                child: self::EMAIL_CHILD_NAME,
                type: EmailType::class,
                options: [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'contact_form.email.placeholder',
                    ]
                ]
            )
            ->add(
                child: self::SUBJECT_CHILD_NAME,
                type: TextType::class,
                options: [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'contact_form.subject.placeholder',
                    ]
                ]
            )
            ->add(
                child: self::MESSAGE_CHILD_NAME,
                type: TextareaType::class,
                options: [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'contact_form.message.placeholder',
                        'rows' => 6
                    ]
                ]
            )
            ->add(
                child: 'submit',
                type: SubmitType::class,
                options: [
                    'label' => 'contact_form.submit.label'
                ]
            )->add(
                child: 'captcha',
                type: Recaptcha3Type::class,
                options: [
                    'action_name' => 'contact',
                    'constraints' => new Recaptcha3([
                        'message' => 'karser_recaptcha3.message',
                        'messageMissingValue' => 'karser_recaptcha3.message_missing_value',
                    ]),
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactRequest::class
        ]);
    }
}
