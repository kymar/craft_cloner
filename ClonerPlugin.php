<?php
/**
 * Cloner plugin for Craft CMS
 *
 * Allows cloning of sections and entry types
 *
 *
 * @author    Kyle Marshall
 * @copyright Copyright (c) 2017 Kyle Marshall
 * @link      kylemarshall.me.uk
 * @package   Cloner
 * @since     0.0.1
 */

namespace Craft;

class ClonerPlugin extends BasePlugin
{
    /**
     * Returns the user-facing name.
     *
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Cloner');
    }

    /**
     * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
     * on the primary plugin class:
     *
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Allows cloning of sections and entry types');
    }

    /**
     * Plugins can have links to their documentation on the Plugins page by adding a getDocumentationUrl() method on
     * the primary plugin class:
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/kymar/cloner/blob/master/README.md';
    }

    /**
     * Plugins can now take part in Craft’s update notifications, and display release notes on the Updates page, by
     * providing a JSON feed that describes new releases, and adding a getReleaseFeedUrl() method on the primary
     * plugin class.
     *
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/kymar/cloner/master/releases.json';
    }

    /**
     * Returns the version number.
     *
     * @return string
     */
    public function getVersion()
    {
        return '0.0.2';
    }

    /**
     * As of Craft 2.5, Craft no longer takes the whole site down every time a plugin’s version number changes, in
     * case there are any new migrations that need to be run. Instead plugins must explicitly tell Craft that they
     * have new migrations by returning a new (higher) schema version number with a getSchemaVersion() method on
     * their primary plugin class:
     *
     * @return string
     */
    public function getSchemaVersion()
    {
        return '0.0.1';
    }

    /**
     * Returns the developer’s name.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Kyle Marshall';
    }

    /**
     * Returns the developer’s website URL.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'kylemarshall.me.uk';
    }

    /**
     * Returns whether the plugin should get its own tab in the CP header.
     *
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }

	/**
	 * Called after the plugin class is instantiated; do any one-time initialization here such as hooks and events:
	 *
	 * @return mixed
	 */
	public function init()
	{
		parent::init();
		if(craft()->request->isCpRequest() && $this->isCraftRequiredVersion())
		{
			$this->includeResources();
		}

		return;
	}

	public function isCraftRequiredVersion()
	{
		return version_compare(craft()->getVersion(), '2.5', '>=');
	}

	protected function includeResources()
	{
		if(!craft()->request->isAjaxRequest() && craft()->userSession->isAdmin())
		{
			craft()->templates->includeTranslations('Handle', 'Entry Types');
			craft()->templates->includeJsResource('cloner/js/Cloner.js');
		}
	}


}
