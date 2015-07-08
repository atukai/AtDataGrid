<?php

namespace AtDataGrid\Form;

use AtDataGrid\Column\Column;
use AtDataGrid\DataGrid;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;

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
     * @param DataGrid $grid
     * @param string $context
     * @param array $data
     * @return Form
     */
    public function build(DataGrid $grid, $context = self::FORM_CONTEXT_CREATE, $data = [])
    {
        if (array_key_exists($context, $this->forms)) {
            return $this->forms[$context];
        }

        /** @var EventManager $em */
        $em = $this->getEventManager();

        $data[self::FORM_CONTEXT_PARAM_NAME] = $context;

        $eventResult = $em->trigger(self::EVENT_GRID_FORM_BUILD_PRE, null, $data)->last();
        if ($eventResult) {
            $data = $eventResult;
        }

        $form = new Form('at-admin-form');
        $inputFilter = $form->getInputFilter();

        // Collect elements
        /** @var Column $column */
        foreach ($grid->getColumns() as $column) {
            if (!$column->isVisibleInForm()) {
                continue;
            }

            /* @var Element */
            $element = $column->getFormElement();

            // Add input filter
            if (!empty($column->getInputFilterSpecification())) {
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

        // Add section elements
        $formSections = $this->getFormSections();

        foreach ($formSections as $name => $section) {
            $fieldSet = new Fieldset($name, ['label' => $section['label']]);
            foreach ($section['elements'] as $element) {
                $fieldSet->add($element);
            }
            $form->add($fieldSet);
        }

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setValue('Save');
        $form->add($submit);

        $form->setInputFilter($inputFilter);

        $em->trigger(self::EVENT_GRID_FORM_BUILD_POST, $form, $data);

        // Set data to form
        if ($data) {
            $form->setData($data);
        }

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
     * @param $options
     * @return $this
     */
    public function addFormSection($name, $options)
    {
        $this->formSections[$name] = $options;
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
     * @return $this
     * @throws \Exception
     */
    public function addFormSectionElement($sectionName, $element, $inputFilterSpecification = [])
    {
        if (! array_key_exists($sectionName, $this->formSections)) {
            throw new \Exception('No section with name "'. $sectionName .'"');
        }

        $this->formSections[$sectionName]['elements'][$element->getName()] = $element;
        return $this;
    }
}