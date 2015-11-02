<?php

namespace JBen87\ParsleyBundle\Form\Type;

use JBen87\ParsleyBundle\Builder\BuilderInterface;
use Symfony\Component\Form\AbstractType as BaseType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Benoit Jouhaud <bjouhaud@prestaconcept.net>
 */
abstract class AbstractType extends BaseType
{
    /**
     * @var BuilderInterface
     */
    private $constraintBuilder;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @param BuilderInterface $constraintBuilder
     */
    public function setConstraintBuilder($constraintBuilder)
    {
        $this->constraintBuilder = $constraintBuilder;
    }

    /**
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer($normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // enable parsley validation
        $view->vars['attr'] += [
            'novalidate' => true,
            'data-parsley-validate' => true,
        ];

        // generate parsley constraints for children and map them as attributes
        foreach ($form as $child) {
            /** @var FormInterface $child */

            $attributes = $child->getConfig()->getAttribute('data_collector/passed_options');

            if (isset($attributes['constraints'])) {
                $this->constraintBuilder->configure([
                    'constraints' => $attributes['constraints'],
                ]);

                foreach ($this->constraintBuilder->build() as $constraint) {
                    $view[$child->getName()]->vars['attr'] += $this->normalizer->normalize($constraint);
                }
            }
        }
    }
}
