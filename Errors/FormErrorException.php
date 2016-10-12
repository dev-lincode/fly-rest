<?php

namespace TagInterativa\RestApi\Bundle\Errors;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class FormErrorException
{
    private $formErrors;
    private $code;

    public function __construct(FormInterface $form,
                                $message = 'An error has occurred while processing your request, make sure your data are valid',
                                $code = 400)
    {
        $this->code = $code;
        $this->buildErrorsTree($form);
    }

    /**
     * @return array
     */
    public function getFormErrors()
    {
        return $this->formErrors;
    }

    /**
     * @param FormInterface $form /**
     *
     */
    private function buildErrorsTree(FormInterface $form)
    {
        $this->formErrors = array();

        $this->formErrors['form_errors'] = array();
        foreach ($form->getErrors() as $error) {
            /** @var $error FormError */
            $message = $error->getMessage();
            array_push($this->formErrors['form_errors'], $message);
        }
        if (!$this->formErrors['form_errors']) {
            unset($this->formErrors['form_errors']);
        }

        $this->formErrors['field_errors'] = array();
        $this->buildFormFieldErrorTree($form);
    }

    /**
     * @param FormInterface $form
     */
    private function buildFormFieldErrorTree(FormInterface $form, $name = null)
    {

        foreach ($form->all() as $key => $child) {
            $children = count($child->all());

            if ($children > 0 && !is_int($key)) {
                $name = $key;
            }

            /** @var $error FormError */
            foreach ($child->getErrors() as $error) {
                $message = $error->getMessage();

                if ($name == null) {
                    $this->formErrors['field_errors'][$key][] = $message;
                } else {
                    $this->formErrors['field_errors'][sprintf('%s-%s-%s', $name, $form->getName(), $key)][] = $message;
                }
            }

            if ($children > 0) {
                $this->buildFormFieldErrorTree($child, $name);
            }
        }
    }
}