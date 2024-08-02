<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('username')
->add('plainPassword', PasswordType::class, [
'label' => 'Password',
'mapped' => false,
'attr' => ['autocomplete' => 'new-password'],
])
->add('roles', ChoiceType::class, [
'choices'  => [
'Admin' => 'ROLE_ADMIN',
'User' => 'ROLE_USER',
],
'expanded' => true,
'multiple' => true,
'label' => 'Roles',
])
->add('agreeTerms', CheckboxType::class, [
'label' => 'Agree to terms',
'mapped' => false,
'required' => true,
])
->add('submit', SubmitType::class, ['label' => 'Register'])
;
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => User::class,
]);
}
}
