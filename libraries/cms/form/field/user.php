<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Field to select a user ID from a modal list.
 *
 * @since  1.6
 */
class JFormFieldUser extends JFormField implements JFormDomfieldinterface
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'User';

	/**
	 * Filtering groups
	 *
	 * @var  array
	 */
	protected $groups = null;

	/**
	 * Users to exclude from the list of users
	 *
	 * @var  array
	 */
	protected $excluded = null;

	/**
	 * Layout to render
	 *
	 * @var  string
	 */
	protected $layout = 'joomla.form.field.user';

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		if (empty($this->layout))
		{
			throw new UnexpectedValueException(sprintf('%s has no layout assigned.', $this->name));
		}

		return $this->getRenderer($this->layout)->render($this->getLayoutData());

	}

	/**
	 * Get the data that is going to be passed to the layout
	 *
	 * @return  array
	 */
	public function getLayoutData()
	{
		// Get the basic field data
		$data = parent::getLayoutData();

		// Load the current username if available.
		$table = JTable::getInstance('user');

		if (is_numeric($this->value))
		{
			$table->load($this->value);
		}
		// Handle the special case for "current".
		elseif (strtoupper($this->value) == 'CURRENT')
		{
			// 'CURRENT' is not a reasonable value to be placed in the html
			$this->value = JFactory::getUser()->id;
			$data['value'] = $this->value;
			$table->load($this->value);
		}
		else
		{
			$table->name = JText::_('JLIB_FORM_SELECT_USER');
		}

		$extraData = array(
				'userName'  => $table->name,
				'groups'    => $this->getGroups(),
				'excluded'  => $this->getExcluded()
		);

		return array_merge($data, $extraData);
	}

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 *
	 * @since   1.6
	 */
	protected function getGroups()
	{
		if (isset($this->element['groups']))
		{
			return explode(',', $this->element['groups']);
		}

		return null;
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 *
	 * @since   1.6
	 */
	protected function getExcluded()
	{
		return explode(',', $this->element['exclude']);
	}

	/**
	 * Transforms the field into an XML element and appends it as child on the given parent. This
	 * is the default implementation of a field. Form fields which do support to be transformed into
	 * an XML Element mut implemet the JFormDomfieldinterface.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   JForm       $form    The form.
	 *
	 * @return DOMElement
	 *
	 * @since 3.7
	 * @see JFormDomfieldinterface::appendXMLFieldTag
	 */
	public function appendXMLFieldTag ($field, DOMElement $parent, JForm $form)
	{
		if (JFactory::getApplication()->isSite())
		{
			// The user field is not working on the front end
			return;
		}

		return parent::appendXMLFieldTag($field, $parent, $form);
	}
}
