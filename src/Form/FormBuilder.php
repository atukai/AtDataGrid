<?php

namespace AtDataGrid\Form;

use AtDataGrid\Column\Column;
use AtDataGrid\DataGrid;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;

class FormBuilder
{
    use EventManagerAwareTrait;

    const FORM_CONTEXT_PARAM_NAME = '__form_context';
    const FORM_CONTEXT_CREATE = 'create';
    const FORM_CONTEXT_EDIT   = 'edit';

    const EVENT_GRID_FORM_BUILD_PRE = 'at-datagrid.form.build.pre';
    const EVENT_GRID_FORM_BUILD_POST = 'at-datagrid.form.build.post';

    /**
     * @var DataGrid
     */
    protected $grid;

    /**
     * @var array
     */
    protected $forms = [];

    /**
     * @var array
     */
    protected $formSections = [];

    /**
     * @var array
     */
    protected $customJs = [];

    /**
     * @param DataGrid $grid
     * @param string $context
     * @param $data
     * @return Form
     */
    public function build(DataGrid $grid, $context = self::FORM_CONTEXT_CREATE, $data = [])
    {
        if (array_key_exists($context, $this->forms)) {
            return $this->forms[$context];
        }

        /** @var EventManager $em */
        $em = $this->getEventManager();

        $eventResult = $em->trigger(self::EVENT_GRID_FORM_BUILD_PRE, null, ['data' => $data, self::FORM_CONTEXT_PARAM_NAME => $context])->last();
        if ($eventResult) {
            $data = $eventResult;
        }

        $form = new Form('at-datagrid-form');
        $inputFilter = $form->getInputFilter();

        // Get form section and create fieldsets
        $formSections = $this->getFormSections();
        foreach ($formSections as $name => $section) {
            $fieldSet = new Fieldset($name, ['label' => $section['label']]);
            $inputFilter->add($section['input_filter'], $name);
            foreach ($section['elements'] as $element) {
                $fieldSet->add($element);
            }
            $form->add($fieldSet);
        }

        // Collect elements and add them to form
        /** @var Column $column */
        foreach ($grid->getColumns() as $column) {
            if (!$column->isVisibleInForm()) {
                continue;
            }

            /* @var Element */
            $element = $column->getFormElement();

            // Add input filter
            if ($column->getInputFilterSpecification()) {
                $inputFilter->add($column->getInputFilterSpecification());
            }

            if (!$element->getLabel()) {
                $element->setLabel($column->getLabel());
            }

            $form->add($element);
        }

        // Add element to prevent CSRF attack
        $csrf = new Element\Csrf('token');
        $csrf->setCsrfValidatorOptions(['timeout' => null]);
        $form->add($csrf);

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setValue('Save');
        $form->add($submit);

        $form->setData($data);
        $form->setInputFilter($inputFilter);

        $em->trigger(self::EVENT_GRID_FORM_BUILD_POST, $form, ['data' => $data, self::FORM_CONTEXT_PARAM_NAME => $context]);

        $this->forms[$context] = $form;
        return $form;
    }

    /**
     * @return array
     */
    public function getFormSections()
    {
        return $this->formSections;
    }

    /**
     * @param $name
     * @param $label
     * @return $this
     */
    public function addFormSection($name, $label)
    {
        $this->formSections[$name] = [
            'label' => $label,
            'elements' => [],
            'input_filter' => new InputFilter()
        ];
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeFormSection($name)
    {
        if (isset($this->formSections[$name])) {
            unset($this->formSections[$name]);
        }

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function clearFormSectionElements($name)
    {
        if (isset($this->formSections[$name])) {
            $this->formSections[$name]['elements'] = [];
        }

        return $this;
    }

    /**
     * @param $sectionName
     * @param $element
     * @param null $inputFilterSpecification
     * @return $this
     * @throws \Exception
     */
    public function addFormSectionElement($sectionName, $element, $inputFilterSpecification = null)
    {
        if (! array_key_exists($sectionName, $this->formSections)) {
            throw new \Exception('No section with name "'. $sectionName .'"');
        }

        $this->formSections[$sectionName]['elements'][$element->getName()] = $element;

        if ($inputFilterSpecification) {
            /** @var InputFilter $inputfilter */
            $inputFilter = $this->formSections[$sectionName]['input_filter'];
            $inputFilter->add($inputFilterSpecification);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomJs()
    {
        return $this->customJs;
    }

    /**
     * @param string $customJs
     */
    public function addCustomJs($customJs)
    {
        $this->customJs[] = $customJs;
    }
}