/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author Gartes | sad.net79@gmail.com | Skype : agroparknew | Telegram : @gartes
 * @date 20.10.2020 21:12
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/* global jQuery , Joomla   */
window.updatewarehouses = function () {
    var $ = jQuery;
    var self = this;
    // Домен сайта
    var host = Joomla.getOptions('GNZ11').Ajax.siteUrl;
    // Медиа версия
    var __v = '';

    this.__type = false;
    this.__plugin = false;
    this.__name = false;
    this._params = {};
    // Параметры Ajax по умолчвнию
    this.AjaxDefaultData = {
        group: this.__type,
        plugin: this.__plugin,
        option: 'com_ajax',
        format: 'json',
        task: null,
    };
    // Default object parameters
    this.ParamsDefaultData = {
        // Медиа версия
        __v: '1.0.0',
        // Режим разработки 
        development_on: false,

        api_key : false,

    }
    // текущая страница
    this.CurrentPage = 1 ;
    this.AddWH = 0 ;
    /**
     * Start Init
     * @constructor
     */
    this.Init = function () {
        this._params = Joomla.getOptions('awnpudp', this.ParamsDefaultData);
        __v = self._params.development_on ? '' : '?v=' + self._params.__v;

        // Параметры Ajax Default
        this.setAjaxDefaultData();
        // Добавить слушателей событий
        this.addEvtListener();

        console.log(this._params)
        console.log(this.AjaxDefaultData);
    }
    /**
     * Добавить слушателей событий
     */
    this.addEvtListener = function (){
        $('#update-warehouses').on('click' , self.onUpdateWarehouses );
    };

    this.onUpdateWarehouses = function ( event ){
        event.preventDefault() ;
        // Кнопка Обновить отделения
        var $bnt = $(this);
        // API-ключ - из формы
        var api_key_in_form = $('#jform_params_np_api_key').val() ;
        // API-ключ - из настроек плагина
        var api_key_param = self._params.api_key ;

        if (api_key_param !== api_key_in_form ){
            alert('Сохраните параметры плагина')
            return  ;
        }
        $bnt.empty().append('<i class="icon-list" title="Обновить отделения"></i>');
        $bnt.append($('<span />' , {
            id : 'countAddWh' ,
            text : ' Добавлено '  ,
        }))
        $('#countAddWh').append('<span>0</span>')

        var data = {
            api :  api_key_param ,
            add_wh :  true ,
            page :  self.CurrentPage ,
            limit :  500 ,
        }
        self.RunAjaxPost( data ) ;
    };

    this.RunAjaxPost = function (data){
        self.AjaxPost(data).then(function (A){
            if ( A.data.count === 0 ) return  ;
            self.CurrentPage +=1 ;
            // еоличество обавленных отделений
            self.AddWH += A.data.count ;

            $('#countAddWh span').text( self.AddWH );

            data.page = self.CurrentPage ;

            self.RunAjaxPost( data ) ;
            console.log( data )
        },function (err){
            console.error( err );
        }) ;
    }


    /**
     * Отправить запрос
     * @param Data - отправляемые данные
     * Должен содержать Data.task = 'taskName';
     * @returns {Promise}
     * @constructor
     */
    this.AjaxPost = function (Data) {
        var data = $.extend(true, this.AjaxDefaultData, Data);
        return new Promise(function (resolve, reject) {
            self.getModul("Ajax").then(function (Ajax) {
                // Не обрабатывать сообщения
                Ajax.ReturnRespond = true;
                // Отправить запрос
                Ajax.send(data, self._params.__name).then(function (r) {
                    resolve(r);
                }, function (err) {
                    console.error(err);
                    reject(err);
                })
            });
        });
    };
    /**
     * Параметры Ajax Default
     */
    this.setAjaxDefaultData = function () {
        this.AjaxDefaultData.group = this._params.__type
        this.AjaxDefaultData.plugin = this._params.__name
    }

};

window.updatewarehouses.prototype = new GNZ11();
window.UpdateWarehouses =  new window.updatewarehouses();
window.UpdateWarehouses.Init();