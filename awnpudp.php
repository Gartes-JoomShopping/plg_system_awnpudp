<?php
defined('_JEXEC') or die;
class PlgSystemAwnpudp extends JPlugin
{
	public function onAjaxAwnpudp()
	{
	    $app = \Joomla\CMS\Factory::getApplication();

        /* UPDATE NP DATA */
        $apikey = trim($this->params->get('np_api_key', ''));
        $chkApi = trim($app->input->get('api', false));
        $addAreas = trim($app->input->get('add_areas', false));
        $addWH = trim($app->input->get('add_wh', false));

		

        # обновления справочника областей городов
		if(($apikey != '') && ( $addAreas ) && ( $chkApi == $apikey ) ) {
			//?option=com_ajax&plugin=awnpudp&format=raw&add_areas=1&api=
			echo self::awNPupdateAreas($apikey);
		}

        # обновления справочника отделений
		if(($apikey != '') && ($addWH  ) && ( $chkApi == $apikey )) {
			//?option=com_ajax&plugin=awnpudp&format=raw&add_wh=1&api=
			echo self::awNPaddWh($apikey);
		}



		/* END: UPDATE NP DATA */
		/* ADDON AJAX REQUEST */
		$npAction	= JRequest::getVar('action');
		$npArea		= JRequest::getVar('area');
		$npCity		= JRequest::getVar('city');
		$npCityRef	= JRequest::getVar('cityRef');
		$npWh		= JRequest::getVar('wh');
		$npLang		= JRequest::getVar('lang');
		
		


		
		
		
		if($npAction == 'getCities') {
			echo self::npGetCities($npArea, $npCity, $npLang);
		}
        /*echo'<pre>';print_r( $npAction );echo'</pre>'.__FILE__.' '.__LINE__;
        
		die(__FILE__ .' '. __LINE__ );*/


		if($npAction == 'getWH') {
			echo self::getWhList($npCityRef, $npLang);
		}

		if($npAction == 'searchWH') {
			if($npWh == 'default') {
				$session = JFactory::getSession();
				$whList = $session->get('defaultWhList');
				if(!$whList) {
					$whList = self::searchWh($npCityRef,$npWh,$npLang);
				}
			} else {
				$whList = self::searchWh($npCityRef,$npWh,$npLang);
			}
			echo $whList;
		}


		/* END: ADDON AJAX REQUEST */
	}

	public function searchWh($npCityRef,$npWh,$npLang)
	{
		$toSession = '0';
		if($npWh == 'default') {
			$npWh = '';
			$toSession = '1';
		}
		$db = JFactory::getDBO();
		$reloadLang = 'ru-RU';
		if(($npLang != 'ru') && ($npLang != 'ru-RU')) {
			$reloadLang = 'uk-UA';
			$qWH = "SELECT #__np_whlist.Description as wh FROM #__np_whlist WHERE #__np_whlist.CityRef = '".$npCityRef."' AND #__np_whlist.Description LIKE '%".$npWh."%'";
		} else {
			$qWH = "SELECT #__np_whlist.DescriptionRu as wh FROM #__np_whlist WHERE #__np_whlist.CityRef = '".$npCityRef."' AND #__np_whlist.Description LIKE '%".$npWh."%'";
		}
		$db->setQuery($qWH);
		$whListArr = $db->loadObjectList();
		if(count($whListArr)) {
			$whList = "";
			foreach($whListArr as $wh) {
				$whList .= '<li class="np-dropdown-item">'.$wh->wh.'</li>';					
			}
		} else {
			$lang = JFactory::getLanguage();
			$extension = 'addon_nova_poshta';
			$base_dir = JPATH_SITE.'/plugins/jshoppingcheckout/addon_nova_poshta';
			$reload = true;
			$lang->load($extension, $base_dir, $reloadLang, $reload);

			$whList = '<li class="np-dropdown-noresult">'.JText::_('_JSHOP_ADDON_NP_SEARCH_NOT_FOUND').'</li>';
		}

		if($toSession == '1') {
			$session = JFactory::getSession();
			$session->set('defaultWhList', $whList);
		}
		return $whList;
	}


	public function getWhList($npCityRef, $npLang)
	{
		$db = JFactory::getDBO();
		$reloadLang = 'ru-RU';
		if(($npLang != 'ru') && ($npLang != 'ru-RU')) {
			$reloadLang = 'uk-UA';
			$qWH = "SELECT #__np_whlist.Number, #__np_whlist.Description as wh FROM #__np_whlist WHERE #__np_whlist.CityRef = '".$npCityRef."'";
		} else {
			$qWH = "SELECT #__np_whlist.Number, #__np_whlist.DescriptionRu as wh FROM #__np_whlist WHERE #__np_whlist.CityRef = '".$npCityRef."'";
		}
		$db->setQuery($qWH);
		$whListArr = $db->loadObjectList();
		if(count($whListArr)) {
			$whList = "";
			foreach($whListArr as $wh) {
				$whList .= '<li class="np-dropdown-item">'.$wh->wh.'</li>';					
			}
		} else {
			$lang = JFactory::getLanguage();
			$extension = 'addon_nova_poshta';
			$base_dir = JPATH_SITE.'/plugins/jshoppingcheckout/addon_nova_poshta';
			$reload = true;
			$lang->load($extension, $base_dir, $reloadLang, $reload);

			$whList = '<li class="np-dropdown-noresult">'.JText::_('_JSHOP_ADDON_NP_SEARCH_NOT_FOUND').'</li>';
		}

		return $whList;
	}


	public function npGetCities($npArea, $npCity, $npLang)
	{
		if($npCity == 'default') {
			$session = JFactory::getSession();
			$cityList = $session->get('defaultCityList');
			if(!$cityList) {
				$cityList = self::npDefatulCities($npLang);
			}
		} else {

	    	$db = JFactory::getDBO();
			$reloadLang = 'ru-RU';
			if(($npLang != 'ru') && ($npLang != 'ru-RU')) {
				$reloadLang = 'uk-UA';
				$q = "SELECT #__np_cities.Description AS cityName, #__np_cities.Ref AS cityRef, #__np_cities.AreaDescription AS areaDesc, #__np_areas.Ref AS areaRef FROM #__np_cities, #__np_areas WHERE #__np_cities.Description LIKE '".$npCity."%' AND #__np_cities.Area = #__np_areas.Ref";
			} else {
				$q = "SELECT #__np_cities.DescriptionRu AS cityName, #__np_cities.Ref AS cityRef, #__np_cities.AreaDescriptionRu AS areaDesc, #__np_areas.Ref AS areaRef FROM #__np_cities, #__np_areas WHERE #__np_cities.DescriptionRU LIKE '".$npCity."%' AND #__np_cities.Area = #__np_areas.Ref";				
			}
	
			$db->setQuery($q);
			$citiesListArr = $db->loadObjectList();
			if(count($citiesListArr)) {
				$cityList = '';
				foreach($citiesListArr as $claItem) {
					$cityList .= '<li data-area="'.$claItem->areaRef.'" data-areaname="'.$claItem->areaDesc.'" data-city="'.$claItem->cityRef.'" class="np-dropdown-item">'.$claItem->cityName.'</li>';
				}
			} else {
				$lang = JFactory::getLanguage();
				$extension = 'addon_nova_poshta';
				$base_dir = JPATH_SITE.'/plugins/jshoppingcheckout/addon_nova_poshta';
				$reload = true;
				$lang->load($extension, $base_dir, $reloadLang, $reload);

				$cityList = '<li class="np-dropdown-noresult">'.JText::_('_JSHOP_ADDON_NP_SEARCH_NOT_FOUND').'</li>';
			}

		}
		return $cityList;
	}

	public function npDefatulCities($npLang) // default City
	{
	    $db = JFactory::getDBO();
		if(($npLang != 'ru') && ($npLang != 'ru-RU')) {
			$np_state_def = 'Рівненська';
			$np_city_def = 'Рівне';
			$q = "SELECT #__np_cities.Description as city, #__np_cities.Ref, #__np_cities.AreaDescription as areaDesc, #__np_cities.Area FROM #__np_cities, #__np_areas WHERE #__np_cities.Ref = #__np_areas.AreasCenter";
		} else {
			$np_state_def = 'Ровенская';
			$np_city_def = 'Ровно';
			$q = "SELECT #__np_cities.DescriptionRu as city, #__np_cities.Ref, #__np_cities.AreaDescriptionRu as areaDesc, #__np_cities.Area FROM #__np_cities, #__np_areas WHERE #__np_cities.Ref = #__np_areas.AreasCenter";
		}
			
		$db->setQuery($q);
		$citiesListArr = $db->loadObjectList();
		if(count($citiesListArr)) {
			$citiesList = '';
			foreach($citiesListArr as $claItem) {
				$citiesList .= '<li data-area="'.$claItem->Area.'" data-areaname="'.$claItem->areaDesc.'" data-city="'.$claItem->Ref.'" class="np-dropdown-item">'.$claItem->city.'</li>';
			}
		} else {
			$citiesList = "API Error! Can't get cities list.";
		}
		$session = JFactory::getSession();
		$session->set('defaultCityList', $citiesList);
		return $citiesList;
	}


	public function awNPupdateAreas($apikey)
	{
		require 'NovaPoshtaApi2.php';
		$np = new NovaPoshtaApi2(
			$apikey,
			'ru', // Язык возвращаемых данных: ru (default) | ua | en
			FALSE, // При ошибке в запросе выбрасывать Exception: FALSE (default) | TRUE
			'curl' // Используемый механизм запроса: curl (defalut) | file_get_content
		);
		
		$areas = $np->getAreas();
		if($areas['success'] == 1) {
			$db = JFactory::getDbo();
			$db->truncateTable('#__np_areas');
			foreach($areas['data'] as $a) {
				$q = "INSERT INTO #__np_areas VALUES ('', '".trim($a['Ref'])."', '".trim($a['AreasCenter'])."', '".trim($a['DescriptionRu'])."', '".trim($a['Description'])."')";
				$db->setQuery($q);
				$db->query();
			}
		}
		
		$cities = $np->getCities();
		if($cities['success'] == 1) {
			$db = JFactory::getDbo();
			$db->truncateTable('#__np_cities');
			foreach($cities['data'] as $a) {
				$q = 'INSERT INTO #__np_cities VALUES ("", "'.trim($a["Description"]).'", "'.trim($a["DescriptionRu"]).'", "'.trim($a["Ref"]).'", "'.trim($a["Delivery1"]).'", "'.trim($a["Delivery2"]).'", "'.trim($a["Delivery3"]).'", "'.trim($a["Delivery4"]).'", "'.trim($a["Delivery5"]).'", "'.trim($a["Delivery6"]).'", "'.trim($a["Delivery7"]).'", "'.trim($a["Area"]).'", "'.trim($a["SettlementType"]).'", "'.trim($a["IsBranch"]).'", "'.$a["PreventEntryNewStreetsUser"].'", "'.$a["Conglomerates"].'", "'.trim($a["CityID"]).'", "'.trim($a["SettlementTypeDescription"]).'", "'.trim($a["SettlementTypeDescriptionRu"]).'", "'.trim($a["SpecialCashCheck"]).'", "'.trim($a["Postomat"]).'", "'.trim($a["AreaDescription"]).'", "'.trim($a["AreaDescriptionRu"]).'")';
				$db->setQuery($q);
				$db->query();
			}
		}

		echo 'Areas: '.count($areas['data']).'<br>';
		echo 'Cities: '.count($cities['data']).'<br>';
	}

    /**
     * обновления справочника отделений
     * @param $apikey
     * @since 3.9
     * @auhtor Gartes | sad.net79@gmail.com | Skype : agroparknew | Telegram : @gartes
     * @date 20.10.2020 20:24
     *
     */
	public function awNPaddWh($apikey)
	{
	    $app = \Joomla\CMS\Factory::getApplication();
		require 'NovaPoshtaApi2.php';
		$np = new NovaPoshtaApi2(
			$apikey,
			'ru', // Язык возвращаемых данных: ru (default) | ua | en
			FALSE, // При ошибке в запросе выбрасывать Exception: FALSE (default) | TRUE
			'curl' // Используемый механизм запроса: curl (defalut) | file_get_content
		);

        $page = $app->input->get('page' , 1 ) ;
        $limit = $app->input->get('limit' , 100 ) ;
		$wh = $np->getWarehouses('' , $page , $limit );

		if($wh['success'] == 1) {



            if ( $page === 1 )
            {
                $this->CleanTable('#__np_whlist' );
            }#END IF

		    $db = JFactory::getDbo();
			foreach($wh['data'] as $a) {
				$q = 'INSERT INTO #__np_whlist VALUES ("" , "'.trim($a["SiteKey"]).'", "'.addslashes(trim($a["Description"])).'", "'.addslashes(trim($a["DescriptionRu"])).'", "'.addslashes(($a["ShortAddress"])).'", "'.addslashes(trim($a["ShortAddressRu"])).'", "'.trim($a["Phone"]).'", "'.trim($a["TypeOfWarehouse"]).'", "'.trim($a["Ref"]).'", "'.trim($a["Number"]).'", "'.trim($a["CityRef"]).'", "'.trim($a["CityDescription"]).'", "'.trim($a["CityDescriptionRu"]).'", "'.trim($a["SettlementRef"]).'", "'.trim($a["WarehouseStatus"]).'", "'.trim($a["CategoryOfWarehouse"]).'");';
//				echo '<p>'.$q.'</p>';
				$db->setQuery($q);
				$db->query();
			}
            $result = [
                'count' => count($wh['data']),
                'page' => $page ,
                'limit' => $limit ,

            ];
            echo new JResponseJson($result , 'Count WH: '.count($wh['data']) );
            die();

			echo '<br>Count WH: '.count($wh['data']);
		}
	}

	protected function CleanTable( $Table ){
        $db = \Joomla\CMS\Factory::getDbo();
        $db->truncateTable( $Table );
    }


}
