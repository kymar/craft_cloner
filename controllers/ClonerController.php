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
		$oldEntryId = craft()->request->getPost('oldEntryId');
		$newEntryName = craft()->request->getPost('newEntryName');
		$newEntryHandle = craft()->request->getPost('newEntryHandle');

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
				$fields[$tab->name][] = $field->fieldId;
				if ($field->required)
				{
					$required[] = $field->fieldId;
				}
			}
		}

		// Set the field layout
		$fieldLayout = craft()->fields->assembleLayout($fields, $required);
		$fieldLayout->type = ElementType::Entry;
		$entryType->setFieldLayout($fieldLayout);

		// Save it
		if (craft()->sections->saveEntryType($entryType))
		{
			craft()->userSession->setNotice(Craft::t('Entry type cloned successfully.'));
			$this->returnJson(['success' => true]);
		}
		else
		{
			craft()->userSession->setError(Craft::t('Couldn’t clone entry type.'));
			$this->returnJson(['success' => false]);
		}
	}

	public function actionCloneSection()
	{
		$oldSectionId = craft()->request->getPost('oldSectionId');
		$newSectionName = craft()->request->getPost('newSectionName');
		$newSectionHandle = craft()->request->getPost('newSectionHandle');

		// Fill new Section with old Section we are cloning from
		$oldSection = craft()->sections->getSectionById($oldSectionId);

		$section = new SectionModel();

		// Shared attributes
		$section->id               = null;
		$section->name             = $newSectionName;
		$section->handle           = $newSectionHandle;
		$section->type             = $oldSection->type;
		$section->enableVersioning = $oldSection->enableVersioning;

		// Type-specific attributes
		$section->hasUrls    = $oldSection->hasUrls;
		$section->template   = $oldSection->template;
		$section->maxLevels  = $oldSection->maxLevels;

		$locales = $oldSection->getLocales();
		foreach ($locales as $locale)
		{
			$locale->urlFormat = $newSectionHandle . '/{slug}';
		}

		$section->setLocales($locales);

		// Save it
		if (craft()->sections->saveSection($section))
		{
			foreach($oldSection->getEntryTypes() as $oldEntryType)
			{
				$entryType = new EntryTypeModel();
				// Set the simple stuff
				$entryType->sectionId = $section->id;
				$entryType->name = $oldEntryType->name;
				$entryType->handle = $oldEntryType->handle;
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
						$fields[$tab->name][] = $field->fieldId;
						if ($field->required)
						{
							$required[] = $field->fieldId;
						}
					}
				}

				// Set the field layout
				$fieldLayout = craft()->fields->assembleLayout($fields, $required);
				$fieldLayout->type = ElementType::Entry;
				$entryType->setFieldLayout($fieldLayout);

				if(!craft()->sections->saveEntryType($entryType))
				{
					craft()->userSession->setError(Craft::t('Couldn’t clone section.'));
					$this->returnJson(['success' => false]);
				}
			}

			craft()->userSession->setNotice(Craft::t('Section cloned successfully.'));
			$this->returnJson(['success' => true]);
		}
		else
		{
			craft()->userSession->setError(Craft::t('Couldn’t clone section.'));
			$this->returnJson(['success' => false]);
		}
	}

}