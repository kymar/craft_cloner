<?php
/**
 * Cloner plugin for Craft CMS
 *
 * Cloner Controller
 *
 * --snip--
 * Generally speaking, controllers are the middlemen between the front end of the CP/website and your plugin’s
 * services. They contain action methods which handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering post data, saving it on a model,
 * passing the model off to a service, and then responding to the request appropriately depending on the service
 * method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what the method does (for example,
 * actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 * --snip--
 *
 * @author    Kyle Marshall
 * @copyright Copyright (c) 2017 Kyle Marshall
 * @link      kylemarshall.me.uk
 * @package   Cloner
 * @since     0.0.1
 */

namespace Craft;

class ClonerController extends BaseController
{

	/**
	 * @var    bool|array Allows anonymous access to this controller's actions.
	 * @access protected
	 */
	protected $allowAnonymous = false;

	/**
	 * Handle a request going to our plugin's index action URL, e.g.: actions/cloner
	 */
	public function actionClone()
	{
//		$sectionId = craft()->request->getPost('sectionId');
//		$oldEntryId = craft()->request->getPost('oldEntryId');
//		$newEntryName = craft()->request->getPost('newEntryName');
//		$newEntryHandle = craft()->request->getPost('newEntryHandle');

		$sectionId = 2;
		$oldEntryId = 2;
		$newEntryName = 'News 2';
		$newEntryHandle = 'news2';

		// Fill new entry with old Entry Type we are cloning from
		$oldEntryType = craft()->sections->getEntryTypeById($oldEntryId);

		$entryType = new EntryTypeModel();
		// Set the simple stuff
		$entryType->sectionId = $oldEntryType->sectionId;
		$entryType->name = $newEntryName;
		$entryType->handle = $newEntryHandle;
		$entryType->hasTitleField = $oldEntryType->hasTitleField;
		$entryType->titleLabel = $oldEntryType->titleLabel;
		$entryType->titleFormat = $oldEntryType->titleFormat;

		$oldFieldLayout = $oldEntryType->getFieldLayout();
		$fields = [];
		$required = [];

		foreach ($oldFieldLayout->getTabs() as $tab)
		{
			$fields[$tab->name] = [];
			foreach ($tab->getFields() as $field)
			{
				$fields[$tab->name][] = $field->id;
				if ($field->required)
				{
					$required[] = $field->id;
				}
			}
		}

		// Set the field layout
		$fieldLayout = craft()->fields->assembleLayout($fields, $required);
		$fieldLayout->type = ElementType::Entry;
		$entryType->setFieldLayout($fieldLayout);

		Craft::dd($entryType->id);
		// Save it
		if (craft()->sections->saveEntryType($entryType))
		{
			$this->returnJson(['success' => Craft::t('Entry type saved.')]);
		}
		else
		{
			$this->returnErrorJson(Craft::t('Couldn’t save entry type.'));
		}

		Craft::dd('done');
	}

}