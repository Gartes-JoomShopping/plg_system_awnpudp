<?php
/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author Gartes | sad.net79@gmail.com | Skype : agroparknew | Telegram : @gartes
 * @date 20.10.2020 21:02
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; // No direct access to this file

jimport('joomla.form.formfield');

class JFormFieldUpdatewarehouses extends JFormField {
    //The field class must know its own type through the variable $type.
    protected $type = 'updatewarehouses';

    protected $__plugin = 'awnpudp' ;
    protected $__type = 'system' ;

    public function getLabel() {}

    public function getInput() {
        $doc = \Joomla\CMS\Factory::getDocument();

        $plugin = \Joomla\CMS\Plugin\PluginHelper::getPlugin($this->__type , $this->__plugin );
        $Registry = new \Joomla\Registry\Registry();
        $params = $Registry->loadObject( json_decode( $plugin->params )) ;






        $this->addLibGnz11();
        \GNZ11\Core\Js::addJproLoad(\Joomla\CMS\Uri\Uri::root().'plugins/system/awnpudp/Assets/js/updatewarehouses.js'    );

        $dataArr = [
            '__type'=>$this->__type ,
            '__name'=>$this->__plugin,
            'api_key'=>$params->get( 'np_api_key' , false ),
        ];

        $doc->addScriptOptions('awnpudp' , $dataArr ) ;

        \Joomla\CMS\Toolbar\ToolbarHelper::divider();

        $bar = JToolBar::getInstance('toolbar'); //ссылка на объект JToolBar
        $title = Text::_('Обновить отделения'); //Надпись на кнопке

        $dhtml = "<a id='update-warehouses' href=\"index.php\" class=\"btn btn-small modal\" >
					<i class=\"icon-list\" title=\"$title\"></i>$title</a>"; //HTML кнопки
        $bar->appendButton('Custom', $dhtml, 'list');//давляем ее на тулбар



    }
    private function addLibGnz11(){
        try
        {
            JLoader::registerNamespace( 'GNZ11' , JPATH_LIBRARIES . '/GNZ11' , $reset = false , $prepend = false , $type = 'psr4' );
            $GNZ11_js =  \GNZ11\Core\Js::instance();
        }
        catch( Exception $e )
        {
            if( !\Joomla\CMS\Filesystem\Folder::exists( $this->patchGnz11 ) && $this->app->isClient('administrator') )
            {
                $this->app->enqueueMessage('Должна быть установлена бибиотека GNZ11' , 'error');
            }#END IF
            throw new \Exception('Должна быть установлена бибиотека GNZ11' , 400 ) ;
        }
    }

}
