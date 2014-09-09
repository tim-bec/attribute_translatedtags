<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTags
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\DcGeneral\Events\Table\Attribute\Translated\Tags;

use ContaoCommunityAlliance\Contao\EventDispatcher\Event\CreateEventDispatcherEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;

/**
 * Handle events for tl_metamodel_attribute.alias_fields.attr_id.
 */
class Subscriber
	extends \MetaModels\DcGeneral\Events\Table\Attribute\Tags\Subscriber
{
	/**
	 * Register all listeners to handle creation of a data container.
	 *
	 * @param CreateEventDispatcherEvent $event The event.
	 *
	 * @return void
	 */
	public static function registerEvents(CreateEventDispatcherEvent $event)
	{
		$dispatcher = $event->getEventDispatcher();

		self::registerBuildDataDefinitionFor(
			'tl_metamodel_attribute',
			$dispatcher,
			__CLASS__ . '::registerTableMetaModelAttributeEvents'
		);
	}

	/**
	 * Register the events for table tl_metamodel_attribute.
	 *
	 * @param BuildDataDefinitionEvent $event The event being processed.
	 *
	 * @return void
	 */
	public static function registerTableMetaModelAttributeEvents(BuildDataDefinitionEvent $event)
	{
		static $registered;
		if ($registered)
		{
			return;
		}
		$registered = true;
		$dispatcher = $event->getDispatcher();

		self::registerListeners(
			array(
				GetPropertyOptionsEvent::NAME => __CLASS__ . '::getColumnNames',
			),
			$dispatcher,
			array('tl_metamodel_attribute', 'tag_langcolumn')
		);

		self::registerListeners(
			array(
				GetPropertyOptionsEvent::NAME => __CLASS__ . '::getTableNames',
			),
			$dispatcher,
			array('tl_metamodel_attribute', 'tag_srctable')
		);

		self::registerListeners(
			array(
				GetPropertyOptionsEvent::NAME => __CLASS__ . '::getSourceColumnNames',
			),
			$dispatcher,
			array('tl_metamodel_attribute', 'tag_srcsorting')
		);
	}

	/**
	 * Retrieve all column names of type int for the current selected table.
	 *
	 * @param GetPropertyOptionsEvent $event The event.
	 *
	 * @return void
	 */
	public static function getSourceColumnNames(GetPropertyOptionsEvent $event)
	{
		$model    = $event->getModel();
		$table    = $model->getProperty('select_srctable');
		$database = \Database::getInstance();

		if (!$table || !$database->tableExists($table))
		{
			return;
		}

		$result = array();

		foreach ($database->listFields($table) as $arrInfo)
		{
			if ($arrInfo['type'] != 'index')
			{
				$result[$arrInfo['name']] = $arrInfo['name'];
			}
		}

		$event->setOptions($result);
	}
}