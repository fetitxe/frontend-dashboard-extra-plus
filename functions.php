<?php
/**
 * Created by my.
 * User: Fetitxe Sanz
 */

/**
 * Append the Version -- Extra Plus
 */
add_filter('fed_plugin_versions', function($version){
	return array_merge($version, array( 
		'extra_plus' => 'Extra Plus ('.FED_EXTRA_PLUS_PLUGIN_VERSION.')'
	));
});

if( !function_exists('fed_form_intl_tel') ){
	/**
	 * Form Telephone.
	 *
	 * @param  array $options  Options.
	 *
	 * @return string
	 */
	function fed_form_intl_tel($options){
		$placeholder = fed_get_data('placeholder', $options);
		$name        = fed_get_data('input_meta', $options);
		$value       = fed_get_data('user_value', $options);
		$class       = 'form-control '.fed_get_data('class_name', $options);
		$required    = fed_get_data('is_required', $options) == 'true' ? 'required="required"' : null;
		$id          = isset( $options['id_name'] ) && ( '' != $options['id_name'] ) ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
		$readonly    = ( true === fed_get_data( 'readonly', $options ) ) ? 'readonly=readonly' : null;
		$disabled    = ( true === fed_get_data( 'disabled', $options ) ) ? 'disabled=disabled' : null;
		$extra       = isset($options['extra']) ? $options['extra'] : null;
		$valid       = __('Valid', 'frontend-dashboard-extra-plus');

		return sprintf(
			"<input type='tel' name='%s_raw' data-hidden='%s' value='%s' class='%s' placeholder='%s' %s %s %s %s %s /><span class='text-success hidden'>✓ %s</span><span class='text-danger hidden'></span>",
			$name,
			$name,
			$value,
			$class,
			$placeholder,
			$disabled,
			$extra,
			$id,
			$readonly,
			$required,
			$valid
		);
	}
}

if( !function_exists('fed_form_geopos') ){
	/**
	 * Form Geolocation with Google Maps v3.
	 *
	 * @param  array $options  Options.
	 *
	 * @return string
	 */
	function fed_form_geopos($options){
		$id          = ( isset( $options['id_name'] ) && '' != $options['id_name'] ) ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
		$name        = fed_get_data('input_meta', $options);
		$value       = fed_get_data('user_value', $options);
		$class       = 'form-control locationFloat '.fed_get_data('class_name', $options);
		$placeholder = ( isset($options['placeholder']) && '' != $options['placeholder'] )? 'placeholder="' . esc_attr($options['placeholder']) . '"' : null;
		$required    = ( 'true' == fed_get_data('is_required', $options) )? 'required="required"' : null;
		$readonly    = ( true === fed_get_data( 'readonly', $options ) )? 'readonly=readonly' : null;
		$disabled    = ( true === fed_get_data( 'disabled', $options ) )? 'disabled=disabled' : null;
		$extended    = isset($options['extended']) && !empty($options['extended'])? unserialize($options['extended']) : array();
		$extra       = isset($options['extra']) ? $options['extra'] : null;

		$latitude = '';
		$longitude = '';
		if( '' != $value){
			$coords = explode(',', $value);
			$latitude = $coords[0];
			$longitude = $coords[1];
		}

		$help = ( isset($extended['show_tooltip_help']) && fed_is_true_false($extended['show_tooltip_help']) )? fed_show_help_message(array(
			'title' 	=> fed_get_data('label_name', $options),
			'content' 	=> $extended['tooltip_help_text'],
		)) : '';

		$hidden = sprintf(
			"<input type='hidden' name='%s' value='%s' class='%s' %s %s %s %s aria-label='Geolocation' />",
			$name,
			$value,
			$class,
			$disabled,
			$extra,
			$readonly,
			$required
		);

		return '<div class="geopos" '.$id.' data-key="'.$name.'" data-locate="'.$extended['geolocation'].'">
			'.$hidden.'
			<div class="input-group">
				<span class="input-group-addon"><span class="visible-xs-inline-block">'.__('Lat.:', 'frontend-dashboard-extra-plus').'</span><span class="hidden-xs">'.__('Latitude:', 'frontend-dashboard-extra-plus').'</span></span>
				<input type="text" class="form-control latitude"  value="'.$latitude.'" aria-label="Latitude" readonly="readonly" '.$placeholder.'>
				<span class="input-group-addon"><span class="visible-xs-inline-block">'.__('Long.:', 'frontend-dashboard-extra-plus').'</span><span class="hidden-xs">'.__('Longitude:', 'frontend-dashboard-extra-plus').'</span></span>
				<input type="text" class="form-control longitude" value="'.$longitude.'" aria-label="Longitude" readonly="readonly" '.$placeholder.'>
				<span class="input-group-btn">
					<button class="btn btn-default setLocation" type="button" data-toggle="modal" data-target=".modal-'.$name.'">'.__('Set Location', 'frontend-dashboard-extra-plus').'</button>
				</span>
			</div>'.$help.'
			<div class="modal fade modal-'.$name.'" tabindex="-1" role="dialog" aria-labelledby="label-'.$name.'">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">'.__('Select a location', 'frontend-dashboard-extra-plus').'</h4>
						</div>
						<div class="modal-body map"></div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger resetLocation">'.__('Clear', 'frontend-dashboard-extra-plus').'</button>
							<button type="button" class="btn btn-success" data-dismiss="modal">'.__('Ok', 'frontend-dashboard-extra-plus').'</button>
						</div>
					</div>
				</div>
			</div>
		</div>';
	}
}

if( !function_exists('fed_form_country') ){
	/**
	 * Country Selector.
	 *
	 * @param  array $options  Options.
	 *
	 * @return string
	 */
	function fed_form_country($options){
		$attrs = array(
			'input_meta' 			=> isset($options['input_meta'])? $options['input_meta'] : fed_get_random_string(5),
			'user_value' 			=> isset($options['user_value'])? $options['user_value'] : '',
			'class_name' 			=> isset($options['class_name'])? $options['class_name'] : '',
			'is_required' 			=> isset($options['is_required'])? $options['is_required'] : '',
			'id_name' 				=> isset($options['id_name'])? $options['id_name'] : '',
			'disabled' 				=> isset($options['disabled'])? $options['disabled'] : '',
			'extra' 				=> isset($options['extra'])? $options['extra'] : '',
		);

		$extended = isset($options['extended'])? ( is_string($options['extended'])? unserialize($options['extended']) : $options['extended'] ) : array();

		if( isset($extended['multiple']) && 'Enable' == $extended['multiple'] ){
			$attrs['extra'] = $attrs['extra'] . ' multiple=multiple ';
			$attrs['input_meta'] = $attrs['input_meta'] . '[]';
			$attrs['class_name'] = $attrs['class_name'] . ' country-multiple ';
		}else{
			$attrs['class_name'] = $attrs['class_name'] . ' country-selector ';
		}

		$countries = fed_country_iso_code();
		if( isset($extended['exclude_countries']) && !empty($extended['exclude_countries']) ){
			foreach( $extended['exclude_countries'] as $code ){
				unset($countries[$code]);
			}
		}
		if( isset($extended['only_countries']) && !empty($extended['only_countries']) ){
			foreach( $countries as $code => $name ){
				if( !in_array($code, $extended['only_countries']) ){
					unset($countries[$code]);
				}
			}
		}
		if( isset($extended['initial_country']) && '' != $extended['initial_country'] ){
			$temp = isset($countries[$extended['initial_country']])? $countries[$extended['initial_country']] : false ;
			if($temp){
				unset($countries[$extended['initial_country']]);
				$countries = array_merge(array(
					$extended['initial_country'] 	=> $temp
				), $countries);
			}
		}

		$attrs['input_value'] = array_merge( array(
			'' 		=> __('Select...', 'frontend-dashboard-extra-plus')
		), $countries);

		return fed_form_select($attrs);
	}
}


if( !function_exists('fed_country_iso_code') ){
	/** National ISO code.
	 *
	 * @return array
	 */
	function fed_country_iso_code(){
		return apply_filters('fed_country_iso_code', array(
			'af' => __('Afghanistan (‫افغانستان‬‎)', 'frontend-dashboard-extra-plus'),
			'al' => __('Albania (Shqipëri)', 'frontend-dashboard-extra-plus'),
			'dz' => __('Algeria (‫الجزائر‬‎)', 'frontend-dashboard-extra-plus'),
			'as' => __('American Samoa', 'frontend-dashboard-extra-plus'),
			'ad' => __('Andorra', 'frontend-dashboard-extra-plus'),
			'ao' => __('Angola', 'frontend-dashboard-extra-plus'),
			'ai' => __('Anguilla', 'frontend-dashboard-extra-plus'),
			'ag' => __('Antigua and Barbuda', 'frontend-dashboard-extra-plus'),
			'ar' => __('Argentina', 'frontend-dashboard-extra-plus'),
			'am' => __('Armenia (Հայաստան)', 'frontend-dashboard-extra-plus'),
			'aw' => __('Aruba', 'frontend-dashboard-extra-plus'),
			'au' => __('Australia', 'frontend-dashboard-extra-plus'),
			'at' => __('Austria (Österreich)', 'frontend-dashboard-extra-plus'),
			'az' => __('Azerbaijan (Azərbaycan)', 'frontend-dashboard-extra-plus'),
			'bs' => __('Bahamas', 'frontend-dashboard-extra-plus'),
			'bh' => __('Bahrain (‫البحرين‬‎)', 'frontend-dashboard-extra-plus'),
			'bd' => __('Bangladesh (বাংলাদেশ)', 'frontend-dashboard-extra-plus'),
			'bb' => __('Barbados', 'frontend-dashboard-extra-plus'),
			'by' => __('Belarus (Беларусь)', 'frontend-dashboard-extra-plus'),
			'be' => __('Belgium (België)', 'frontend-dashboard-extra-plus'),
			'bz' => __('Belize', 'frontend-dashboard-extra-plus'),
			'bj' => __('Benin (Bénin)', 'frontend-dashboard-extra-plus'),
			'bm' => __('Bermuda', 'frontend-dashboard-extra-plus'),
			'bt' => __('Bhutan (འབྲུག)', 'frontend-dashboard-extra-plus'),
			'bo' => __('Bolivia', 'frontend-dashboard-extra-plus'),
			'ba' => __('Bosnia and Herzegovina (Босна и Херцеговина)', 'frontend-dashboard-extra-plus'),
			'bw' => __('Botswana', 'frontend-dashboard-extra-plus'),
			'br' => __('Brazil (Brasil)', 'frontend-dashboard-extra-plus'),
			'io' => __('British Indian Ocean Territory', 'frontend-dashboard-extra-plus'),
			'vg' => __('British Virgin Islands', 'frontend-dashboard-extra-plus'),
			'bn' => __('Brunei', 'frontend-dashboard-extra-plus'),
			'bg' => __('Bulgaria (България)', 'frontend-dashboard-extra-plus'),
			'bf' => __('Burkina Faso', 'frontend-dashboard-extra-plus'),
			'bi' => __('Burundi (Uburundi)', 'frontend-dashboard-extra-plus'),
			'kh' => __('Cambodia (កម្ពុជា)', 'frontend-dashboard-extra-plus'),
			'cm' => __('Cameroon (Cameroun)', 'frontend-dashboard-extra-plus'),
			'ca' => __('Canada', 'frontend-dashboard-extra-plus'),
			'cv' => __('Cape Verde (Kabu Verdi)', 'frontend-dashboard-extra-plus'),
			'bq' => __('Caribbean Netherlands', 'frontend-dashboard-extra-plus'),
			'ky' => __('Cayman Islands', 'frontend-dashboard-extra-plus'),
			'cf' => __('Central African Republic (République centrafricaine)', 'frontend-dashboard-extra-plus'),
			'td' => __('Chad (Tchad)', 'frontend-dashboard-extra-plus'),
			'cl' => __('Chile', 'frontend-dashboard-extra-plus'),
			'cn' => __('China (中国)', 'frontend-dashboard-extra-plus'),
			'cx' => __('Christmas Island', 'frontend-dashboard-extra-plus'),
			'cc' => __('Cocos (Keeling) Islands', 'frontend-dashboard-extra-plus'),
			'co' => __('Colombia', 'frontend-dashboard-extra-plus'),
			'km' => __('Comoros (‫جزر القمر‬‎)', 'frontend-dashboard-extra-plus'),
			'cd' => __('Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)', 'frontend-dashboard-extra-plus'),
			'cg' => __('Congo (Republic) (Congo-Brazzaville)', 'frontend-dashboard-extra-plus'),
			'ck' => __('Cook Islands', 'frontend-dashboard-extra-plus'),
			'cr' => __('Costa Rica', 'frontend-dashboard-extra-plus'),
			'ci' => __('Côte d’Ivoire', 'frontend-dashboard-extra-plus'),
			'hr' => __('Croatia (Hrvatska)', 'frontend-dashboard-extra-plus'),
			'cu' => __('Cuba', 'frontend-dashboard-extra-plus'),
			'cw' => __('Curaçao', 'frontend-dashboard-extra-plus'),
			'cy' => __('Cyprus (Κύπρος)', 'frontend-dashboard-extra-plus'),
			'cz' => __('Czech Republic (Česká republika)', 'frontend-dashboard-extra-plus'),
			'dk' => __('Denmark (Danmark)', 'frontend-dashboard-extra-plus'),
			'dj' => __('Djibouti', 'frontend-dashboard-extra-plus'),
			'dm' => __('Dominica', 'frontend-dashboard-extra-plus'),
			'do' => __('Dominican Republic (República Dominicana)', 'frontend-dashboard-extra-plus'),
			'ec' => __('Ecuador', 'frontend-dashboard-extra-plus'),
			'eg' => __('Egypt (‫مصر‬‎)', 'frontend-dashboard-extra-plus'),
			'sv' => __('El Salvador', 'frontend-dashboard-extra-plus'),
			'gq' => __('Equatorial Guinea (Guinea Ecuatorial)', 'frontend-dashboard-extra-plus'),
			'er' => __('Eritrea', 'frontend-dashboard-extra-plus'),
			'ee' => __('Estonia (Eesti)', 'frontend-dashboard-extra-plus'),
			'et' => __('Ethiopia', 'frontend-dashboard-extra-plus'),
			'fk' => __('Falkland Islands (Islas Malvinas)', 'frontend-dashboard-extra-plus'),
			'fo' => __('Faroe Islands (Føroyar)', 'frontend-dashboard-extra-plus'),
			'fj' => __('Fiji', 'frontend-dashboard-extra-plus'),
			'fi' => __('Finland (Suomi)', 'frontend-dashboard-extra-plus'),
			'fr' => __('France', 'frontend-dashboard-extra-plus'),
			'gf' => __('French Guiana (Guyane française)', 'frontend-dashboard-extra-plus'),
			'pf' => __('French Polynesia (Polynésie française)', 'frontend-dashboard-extra-plus'),
			'ga' => __('Gabon', 'frontend-dashboard-extra-plus'),
			'gm' => __('Gambia', 'frontend-dashboard-extra-plus'),
			'ge' => __('Georgia (საქართველო)', 'frontend-dashboard-extra-plus'),
			'de' => __('Germany (Deutschland)', 'frontend-dashboard-extra-plus'),
			'gh' => __('Ghana (Gaana)', 'frontend-dashboard-extra-plus'),
			'gi' => __('Gibraltar', 'frontend-dashboard-extra-plus'),
			'gr' => __('Greece (Ελλάδα)', 'frontend-dashboard-extra-plus'),
			'gl' => __('Greenland (Kalaallit Nunaat)', 'frontend-dashboard-extra-plus'),
			'gd' => __('Grenada', 'frontend-dashboard-extra-plus'),
			'gp' => __('Guadeloupe', 'frontend-dashboard-extra-plus'),
			'gu' => __('Guam', 'frontend-dashboard-extra-plus'),
			'gt' => __('Guatemala', 'frontend-dashboard-extra-plus'),
			'gg' => __('Guernsey', 'frontend-dashboard-extra-plus'),
			'gn' => __('Guinea (Guinée)', 'frontend-dashboard-extra-plus'),
			'gw' => __('Guinea-Bissau (Guiné Bissau)', 'frontend-dashboard-extra-plus'),
			'gy' => __('Guyana', 'frontend-dashboard-extra-plus'),
			'ht' => __('Haiti', 'frontend-dashboard-extra-plus'),
			'hn' => __('Honduras', 'frontend-dashboard-extra-plus'),
			'hk' => __('Hong Kong (香港)', 'frontend-dashboard-extra-plus'),
			'hu' => __('Hungary (Magyarország)', 'frontend-dashboard-extra-plus'),
			'is' => __('Iceland (Ísland)', 'frontend-dashboard-extra-plus'),
			'in' => __('India (भारत)', 'frontend-dashboard-extra-plus'),
			'id' => __('Indonesia', 'frontend-dashboard-extra-plus'),
			'ir' => __('Iran (‫ایران‬‎)', 'frontend-dashboard-extra-plus'),
			'iq' => __('Iraq (‫العراق‬‎)', 'frontend-dashboard-extra-plus'),
			'ie' => __('Ireland', 'frontend-dashboard-extra-plus'),
			'im' => __('Isle of Man', 'frontend-dashboard-extra-plus'),
			'il' => __('Israel (‫ישראל‬‎)', 'frontend-dashboard-extra-plus'),
			'it' => __('Italy (Italia)', 'frontend-dashboard-extra-plus'),
			'jm' => __('Jamaica', 'frontend-dashboard-extra-plus'),
			'jp' => __('Japan (日本)', 'frontend-dashboard-extra-plus'),
			'je' => __('Jersey', 'frontend-dashboard-extra-plus'),
			'jo' => __('Jordan (‫الأردن‬‎)', 'frontend-dashboard-extra-plus'),
			'kz' => __('Kazakhstan (Казахстан)', 'frontend-dashboard-extra-plus'),
			'ke' => __('Kenya', 'frontend-dashboard-extra-plus'),
			'ki' => __('Kiribati', 'frontend-dashboard-extra-plus'),
			'xk' => __('Kosovo', 'frontend-dashboard-extra-plus'),
			'kw' => __('Kuwait (‫الكويت‬‎)', 'frontend-dashboard-extra-plus'),
			'kg' => __('Kyrgyzstan (Кыргызстан)', 'frontend-dashboard-extra-plus'),
			'la' => __('Laos (ລາວ)', 'frontend-dashboard-extra-plus'),
			'lv' => __('Latvia (Latvija)', 'frontend-dashboard-extra-plus'),
			'lb' => __('Lebanon (‫لبنان‬‎)', 'frontend-dashboard-extra-plus'),
			'ls' => __('Lesotho', 'frontend-dashboard-extra-plus'),
			'lr' => __('Liberia', 'frontend-dashboard-extra-plus'),
			'ly' => __('Libya (‫ليبيا‬‎)', 'frontend-dashboard-extra-plus'),
			'li' => __('Liechtenstein', 'frontend-dashboard-extra-plus'),
			'lt' => __('Lithuania (Lietuva)', 'frontend-dashboard-extra-plus'),
			'lu' => __('Luxembourg', 'frontend-dashboard-extra-plus'),
			'mo' => __('Macau (澳門)', 'frontend-dashboard-extra-plus'),
			'mk' => __('Macedonia (FYROM) (Македонија)', 'frontend-dashboard-extra-plus'),
			'mg' => __('Madagascar (Madagasikara)', 'frontend-dashboard-extra-plus'),
			'mw' => __('Malawi', 'frontend-dashboard-extra-plus'),
			'my' => __('Malaysia', 'frontend-dashboard-extra-plus'),
			'mv' => __('Maldives', 'frontend-dashboard-extra-plus'),
			'ml' => __('Mali', 'frontend-dashboard-extra-plus'),
			'mt' => __('Malta', 'frontend-dashboard-extra-plus'),
			'mh' => __('Marshall Islands', 'frontend-dashboard-extra-plus'),
			'mq' => __('Martinique', 'frontend-dashboard-extra-plus'),
			'mr' => __('Mauritania (‫موريتانيا‬‎)', 'frontend-dashboard-extra-plus'),
			'mu' => __('Mauritius (Moris)', 'frontend-dashboard-extra-plus'),
			'yt' => __('Mayotte', 'frontend-dashboard-extra-plus'),
			'mx' => __('Mexico (México)', 'frontend-dashboard-extra-plus'),
			'fm' => __('Micronesia', 'frontend-dashboard-extra-plus'),
			'md' => __('Moldova (Republica Moldova)', 'frontend-dashboard-extra-plus'),
			'mc' => __('Monaco', 'frontend-dashboard-extra-plus'),
			'mn' => __('Mongolia (Монгол)', 'frontend-dashboard-extra-plus'),
			'me' => __('Montenegro (Crna Gora)', 'frontend-dashboard-extra-plus'),
			'ms' => __('Montserrat', 'frontend-dashboard-extra-plus'),
			'ma' => __('Morocco (‫المغرب‬‎)', 'frontend-dashboard-extra-plus'),
			'mz' => __('Mozambique (Moçambique)', 'frontend-dashboard-extra-plus'),
			'mm' => __('Myanmar (Burma) (မြန်မာ)', 'frontend-dashboard-extra-plus'),
			'na' => __('Namibia (Namibië)', 'frontend-dashboard-extra-plus'),
			'nr' => __('Nauru', 'frontend-dashboard-extra-plus'),
			'np' => __('Nepal (नेपाल)', 'frontend-dashboard-extra-plus'),
			'nl' => __('Netherlands (Nederland)', 'frontend-dashboard-extra-plus'),
			'nc' => __('New Caledonia (Nouvelle-Calédonie)', 'frontend-dashboard-extra-plus'),
			'nz' => __('New Zealand', 'frontend-dashboard-extra-plus'),
			'ni' => __('Nicaragua', 'frontend-dashboard-extra-plus'),
			'ne' => __('Niger (Nijar)', 'frontend-dashboard-extra-plus'),
			'ng' => __('Nigeria', 'frontend-dashboard-extra-plus'),
			'nu' => __('Niue', 'frontend-dashboard-extra-plus'),
			'nf' => __('Norfolk Island', 'frontend-dashboard-extra-plus'),
			'kp' => __('North Korea (조선 민주주의 인민 공화국)', 'frontend-dashboard-extra-plus'),
			'mp' => __('Northern Mariana Islands', 'frontend-dashboard-extra-plus'),
			'no' => __('Norway (Norge)', 'frontend-dashboard-extra-plus'),
			'om' => __('Oman (‫عُمان‬‎)', 'frontend-dashboard-extra-plus'),
			'pk' => __('Pakistan (‫پاکستان‬‎)', 'frontend-dashboard-extra-plus'),
			'pw' => __('Palau', 'frontend-dashboard-extra-plus'),
			'ps' => __('Palestine (‫فلسطين‬‎)', 'frontend-dashboard-extra-plus'),
			'pa' => __('Panama (Panamá)', 'frontend-dashboard-extra-plus'),
			'pg' => __('Papua New Guinea', 'frontend-dashboard-extra-plus'),
			'py' => __('Paraguay', 'frontend-dashboard-extra-plus'),
			'pe' => __('Peru (Perú)', 'frontend-dashboard-extra-plus'),
			'ph' => __('Philippines', 'frontend-dashboard-extra-plus'),
			'pl' => __('Poland (Polska)', 'frontend-dashboard-extra-plus'),
			'pt' => __('Portugal', 'frontend-dashboard-extra-plus'),
			'pr' => __('Puerto Rico', 'frontend-dashboard-extra-plus'),
			'qa' => __('Qatar (‫قطر‬‎)', 'frontend-dashboard-extra-plus'),
			're' => __('Réunion (La Réunion)', 'frontend-dashboard-extra-plus'),
			'ro' => __('Romania (România)', 'frontend-dashboard-extra-plus'),
			'ru' => __('Russia (Россия)', 'frontend-dashboard-extra-plus'),
			'rw' => __('Rwanda', 'frontend-dashboard-extra-plus'),
			'bl' => __('Saint Barthélemy', 'frontend-dashboard-extra-plus'),
			'sh' => __('Saint Helena', 'frontend-dashboard-extra-plus'),
			'kn' => __('Saint Kitts and Nevis', 'frontend-dashboard-extra-plus'),
			'lc' => __('Saint Lucia', 'frontend-dashboard-extra-plus'),
			'mf' => __('Saint Martin (Saint-Martin (partie française))', 'frontend-dashboard-extra-plus'),
			'pm' => __('Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)', 'frontend-dashboard-extra-plus'),
			'vc' => __('Saint Vincent and the Grenadines', 'frontend-dashboard-extra-plus'),
			'ws' => __('Samoa', 'frontend-dashboard-extra-plus'),
			'sm' => __('San Marino', 'frontend-dashboard-extra-plus'),
			'st' => __('São Tomé and Príncipe (São Tomé e Príncipe)', 'frontend-dashboard-extra-plus'),
			'sa' => __('Saudi Arabia (‫المملكة العربية السعودية‬‎)', 'frontend-dashboard-extra-plus'),
			'sn' => __('Senegal (Sénégal)', 'frontend-dashboard-extra-plus'),
			'rs' => __('Serbia (Србија)', 'frontend-dashboard-extra-plus'),
			'sc' => __('Seychelles', 'frontend-dashboard-extra-plus'),
			'sl' => __('Sierra Leone', 'frontend-dashboard-extra-plus'),
			'sg' => __('Singapore', 'frontend-dashboard-extra-plus'),
			'sx' => __('Sint Maarten', 'frontend-dashboard-extra-plus'),
			'sk' => __('Slovakia (Slovensko)', 'frontend-dashboard-extra-plus'),
			'si' => __('Slovenia (Slovenija)', 'frontend-dashboard-extra-plus'),
			'sb' => __('Solomon Islands', 'frontend-dashboard-extra-plus'),
			'so' => __('Somalia (Soomaaliya)', 'frontend-dashboard-extra-plus'),
			'za' => __('South Africa', 'frontend-dashboard-extra-plus'),
			'kr' => __('South Korea (대한민국)', 'frontend-dashboard-extra-plus'),
			'ss' => __('South Sudan (‫جنوب السودان‬‎)', 'frontend-dashboard-extra-plus'),
			'es' => __('Spain (España)', 'frontend-dashboard-extra-plus'),
			'lk' => __('Sri Lanka (ශ්‍රී ලංකාව)', 'frontend-dashboard-extra-plus'),
			'sd' => __('Sudan (‫السودان‬‎)', 'frontend-dashboard-extra-plus'),
			'sr' => __('Suriname', 'frontend-dashboard-extra-plus'),
			'sj' => __('Svalbard and Jan Mayen', 'frontend-dashboard-extra-plus'),
			'sz' => __('Swaziland', 'frontend-dashboard-extra-plus'),
			'se' => __('Sweden (Sverige)', 'frontend-dashboard-extra-plus'),
			'ch' => __('Switzerland (Schweiz)', 'frontend-dashboard-extra-plus'),
			'sy' => __('Syria (‫سوريا‬‎)', 'frontend-dashboard-extra-plus'),
			'tw' => __('Taiwan (台灣)', 'frontend-dashboard-extra-plus'),
			'tj' => __('Tajikistan', 'frontend-dashboard-extra-plus'),
			'tz' => __('Tanzania', 'frontend-dashboard-extra-plus'),
			'th' => __('Thailand (ไทย)', 'frontend-dashboard-extra-plus'),
			'tl' => __('Timor-Leste', 'frontend-dashboard-extra-plus'),
			'tg' => __('Togo', 'frontend-dashboard-extra-plus'),
			'tk' => __('Tokelau', 'frontend-dashboard-extra-plus'),
			'to' => __('Tonga', 'frontend-dashboard-extra-plus'),
			'tt' => __('Trinidad and Tobago', 'frontend-dashboard-extra-plus'),
			'tn' => __('Tunisia (‫تونس‬‎)', 'frontend-dashboard-extra-plus'),
			'tr' => __('Turkey (Türkiye)', 'frontend-dashboard-extra-plus'),
			'tm' => __('Turkmenistan', 'frontend-dashboard-extra-plus'),
			'tc' => __('Turks and Caicos Islands', 'frontend-dashboard-extra-plus'),
			'tv' => __('Tuvalu', 'frontend-dashboard-extra-plus'),
			'vi' => __('U.S. Virgin Islands', 'frontend-dashboard-extra-plus'),
			'ug' => __('Uganda', 'frontend-dashboard-extra-plus'),
			'ua' => __('Ukraine (Україна)', 'frontend-dashboard-extra-plus'),
			'ae' => __('United Arab Emirates (‫الإمارات العربية المتحدة‬‎)', 'frontend-dashboard-extra-plus'),
			'gb' => __('United Kingdom', 'frontend-dashboard-extra-plus'),
			'us' => __('United States', 'frontend-dashboard-extra-plus'),
			'uy' => __('Uruguay', 'frontend-dashboard-extra-plus'),
			'uz' => __('Uzbekistan (Oʻzbekiston)', 'frontend-dashboard-extra-plus'),
			'vu' => __('Vanuatu', 'frontend-dashboard-extra-plus'),
			'va' => __('Vatican City (Città del Vaticano)', 'frontend-dashboard-extra-plus'),
			've' => __('Venezuela', 'frontend-dashboard-extra-plus'),
			'vn' => __('Vietnam (Việt Nam)', 'frontend-dashboard-extra-plus'),
			'wf' => __('Wallis and Futuna (Wallis-et-Futuna)', 'frontend-dashboard-extra-plus'),
			'eh' => __('Western Sahara (‫الصحراء الغربية‬‎)', 'frontend-dashboard-extra-plus'),
			'ye' => __('Yemen (‫اليمن‬‎)', 'frontend-dashboard-extra-plus'),
			'zm' => __('Zambia', 'frontend-dashboard-extra-plus'),
			'zw' => __('Zimbabwe', 'frontend-dashboard-extra-plus'),
			'ax' => __('Åland Islands', 'frontend-dashboard-extra-plus')
		));
	}
}

if( !function_exists('fed_form_file_advanced') ){
	/* Draw file upload.
	 *
	 * @param  array $options  Options.
	 *
	 * @return string
	 */
	function fed_form_file_advanced($options){
		$extended = isset($options['extended'])? unserialize($options['extended']) : array();
		$all_mime = array_values(wp_get_mime_types());
		$allow_mime = ( isset($extended['allowed_files']) && !empty($extended['allowed_files']) )? $extended['allowed_files'] : $all_mime;
		$forbiden_mime = ( isset($extended['forbidden_files']) && !empty($extended['forbidden_files']) )? $extended['forbidden_files'] : array();
		$multiple = ( isset($extended['multiple_files']) && 'true' == $extended['multiple_files'] )? true : false;
		$label = isset($options['label_name'])? $options['label_name'] : __('Feature');
		$meta = $options['input_meta'].($multiple? '[]' : '');
		$icon = isset($extended['main_icon'])? $extended['main_icon'] : 'fas fa-upload';

		$items = array();
		$user_value = fed_get_data('user_value', $options);
		if( !empty($user_value) ){
			$user_value = $multiple? unserialize($user_value) : $user_value;
			if( !is_array($user_value) ){
				$user_value = array($user_value);
			}
			foreach( $user_value as $value ){
				if( '' !== $value){
					$value = (int) $value;
					$items[] = array(
						'id' 	=> $value,
						'img'	=> fed_extra_plus_get_image_by_type($value, array(
							'width' 		=> $multiple? 112 : 250,
							'height' 		=> $multiple? 112 : 250,
							'cut' 			=> true
						)),
					);
				}
			}
		}
		if( empty($user_value) || $multiple ){
			$items[] = array(
				'id' 	=> '',
				'img'	=> '<span class="fed_extra_plus_upload_icon '.$icon .' fa-3x"></span>',
			);
		}

		$all_mime = array_values(array_intersect(array_diff($all_mime, $forbiden_mime), $allow_mime));
		$input = '<div class="fed_extra_plus_upload_wrapper text-center" data-multiple="'.$multiple.'" data-meta="'.$meta.'" data-icon="'.$icon.'" data-label="'.$label.'" data-button="'.__('Select').'" data-url="'.admin_url('admin-ajax.php').'" data-nonce="'.wp_create_nonce('fed_nonce').'" data-allow="'.implode(',', $all_mime).'">';
		foreach( $items as $attach ){
			$input .= fed_extra_plus_attached_item($meta, $attach);
		}
		$input .= '</div>';
		return $input;
	}
}

if( !function_exists('fed_extra_plus_attached_item') ){
	/** Generate de input w/ values
	 *
	 * @param $id  Attachment id
	 *
	 * @return string
	 */
	function fed_extra_plus_attached_item($meta, $item = array() ){
		$value = ( isset($item['id']) && '' !== $item['id'] )? (int) $item['id'] : '';
		$empty = ( !isset($item['id']) || ( isset($item['id']) && '' === $item['id'] ) )? 'empty' : '';
		$img   = ( isset($item['img']) && '' !== $item['img'] )? $item['img'] : '';
		return '<div class="thumbnail '.$empty.'">
			<button type="button" class="close fed_extra_plus_remove_image" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<div class="fed_extra_plus_upload_container">
				<div class="fed_extra_plus_upload_image_container">'.$img.'</div>
				<input type="hidden" name="'.$meta.'" class="fed_extra_plus_upload_input" value="'.$value.'" />
			</div>
		</div>';
	}
}

if( !function_exists('fed_extra_plus_get_image_by_type') ){
	/** Return the attached image for use
	 *
	 * @param $id  Attachment id
	 *
	 * @return string
	 */
	function fed_extra_plus_get_image_by_type($id, $crop = array()){
		$opts = array(
			'width' 	=> ( isset($crop['width']) && '' !== $crop['width'] )? (int) $crop['width'] : 250,
			'height' 	=> ( isset($crop['height']) && '' !== $crop['height'] )? (int) $crop['height'] : 250,
			'cut' 		=> ( isset($crop['cut']) && is_bool($crop['cut']) )? $crop['cut'] : false,
		);
		$mime_type = get_post_mime_type($id);
		$default = fed_image_mime_types();
		if( false !== strpos($mime_type, 'image') ){
			return wp_get_attachment_image($id, array($opts['width'], $opts['height'], $opts['cut']));
		}
		$file_name = '<span class="file_name">'.basename(get_attached_file($id)).'</span>';
		if( isset($default[$mime_type]) ){
			return '<img class="img-responsive center-block" src="'.$default[$mime_type].'" />'.$file_name;
		}
		return '<img class="img-responsive center-block" src="'.site_url().'/wp-includes/images/media/default.png" />'.$file_name;
	}
}

if( !function_exists('fed_extra_plus_ajax_get_attachment_avanced_file') ){
	/** Return the attached image on ajax request
	 *
	 * @param $id  Attachment id
	 *
	 * @return string
	 */
	function fed_extra_plus_ajax_get_attachment_avanced_file(){
		$request = fed_sanitize_text_field($_REQUEST);
		fed_verify_nonce($request);

		$icon = '<span class="fed_extra_plus_upload_icon '.$request['icon'].' fa-3x"></span>';
		$multiple = ( isset($request['multiple']) && '1' === $request['multiple'] )? true : false;

		wp_send_json_success(array(
			'data' => array(
				'img' 		=> fed_extra_plus_get_image_by_type($request['attached'], array(
					'width' 	=> $multiple? 112 : 250,
					'height' 	=> $multiple? 112 : 250,
					'cut' 		=> true
				)),
				'append' 	=> $multiple? fed_extra_plus_attached_item($request['meta'], array('img' => $icon)) : false,
			),
		));
	}
	add_action('wp_ajax_fed_extra_plus_ajax_get_attachment_avanced_file', 'fed_extra_plus_ajax_get_attachment_avanced_file');
}
?>