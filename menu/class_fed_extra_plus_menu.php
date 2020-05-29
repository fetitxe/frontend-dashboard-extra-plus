<?php

if( !defined('ABSPATH') ) exit;

if( !class_exists('Fed_Extra_Plus_Menu')){
	/** Fed_Extra_Plus_Menu
	 */
	class Fed_Extra_Plus_Menu{
		/** Fed_Extra_Plus_Menu constructor.
		 */
		public function __construct(){
			add_filter('fed_custom_input_fields', array($this, 'fed_extra_plus_custom_input_fields'), 10, 2);
			add_action('fed_admin_input_item_options', array($this, 'fed_extra_plus_admin_input_item_options'), 10, 1);
			add_action('fed_admin_input_fields_container_extra', array($this, 'fed_extra_plus_admin_input_fields_container_extra_phone'), 10, 3);

			add_filter('fed_default_extended_fields', array($this, 'fed_extra_plus_default_extended_fields'), 10, 1);
			add_filter('fed_process_form_fields', array($this, 'fed_extra_plus_process_form_fields'), 10, 4);

			add_action('fed_enqueue_script_style_admin', array($this, 'fed_extra_plus_enqueue_script_style'));
			add_action('fed_enqueue_script_style_frontend', array($this, 'fed_extra_plus_enqueue_script_style'));
		}

		/** Draw Telephone field
		 *
		 * @param $input
		 * @param $attr
		 *
		 * @return string
		 */
		public function fed_extra_plus_custom_input_fields($input, $attr){
			switch( $attr['input_type'] ){
				case 'intl-tel':
					$extended = array();
					if( isset($attr['extended']) ){
						$extended = $attr['extended'];
						if( is_string($extended) ){
							$extended = unserialize($extended);
						}
					}
					$data = '';
					foreach( $extended as $key => $value ){
						if( is_array($value) ){
							$value = json_encode($value);
						}
						$data .= 'data-' . $key . '=\'' . $value . '\' ';
					}
					$attr['extra'] = $data;
					$input .= fed_form_intl_tel($attr);
					break;
			}
			return $input;
		}

		/** Append Telephone Item
		 *
		 * @param  array  $items
		 *
		 * @return array
		 */
		public function fed_extra_plus_admin_input_item_options($items){
			return array_merge($items, array(
				'intl-tel'      => array(
					'name'  => __('Telephone', 'frontend-dashboard-extra-plus'),
					'image' => plugins_url('assets/img/inputs/phone.png', FED_EXTRA_PLUS_PLUGIN),
				),
			));
		}

		/** International Telephone Field
		 *
		 * @param  array  $row
		 * @param  string  $action 
		 * @param  array  $menu_options
		 */
		public function fed_extra_plus_admin_input_fields_container_extra_phone($row, $action, $menu_options){
			?><div class="row fed_input_type_container fed_input_intl-tel_container hide">
				<form method="post" class="fed_admin_menu fed_ajax" action="<?php echo admin_url('admin-ajax.php?action=fed_admin_setting_up_form'); ?>"><?php
					wp_nonce_field('fed_nonce', 'fed_nonce');
					echo fed_loader();
					?><div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<b><?php _e('International Telephone', 'frontend-dashboard-extra-plus'); ?></b>
								</h3>
							</div>
							<div class="panel-body">
								<div class="fed_input_text">
									<?php fed_get_admin_up_label_input_order($row); ?>
									<div class="row">
										<?php fed_get_admin_up_input_meta($row); ?>
										<div class="form-group col-md-3">
											<label for="class_name"><?php _e('Class Name'); ?></label><?php 
											echo fed_input_box('class_name', array(
												'value' => $row['class_name']
											),'single_line');
										?></div>
										<div class="form-group col-md-3">
											<label for="id_name"><?php _e('ID Name'); ?></label><?php 
											echo fed_input_box('id_name', array(
												'value' => $row['id_name']
											), 'single_line'); 
										?></div>
									</div>
									<div class="row">
										<div class="form-group col-md-3">
											<label for="validate"><?php _e('Make validation', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Make validation', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('Checks user value is valid phone.', 'frontend-dashboard-extra-plus')
												));
											?></label><?php
											echo fed_input_box('validate', array(
												'name'    => 'extended[validate]',
												'value'   => isset($row['extended']['validate'])? $row['extended']['validate'] : 'false',
												'options' => array(
													'false' 	=> __('No'),
													'true' 		=> __('Yes'),
												),
											), 'select');
										?></div>
										<div class="form-group col-md-3">
											<label for="dial_code"><?php _e('Dial Code', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Dial Code', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('Display the country dial code next to the selected flag.','frontend-dashboard-extra-plus')
												));
											?></label><?php
											echo fed_input_box('dial_code', array(
												'name'    => 'extended[dial_code]',
												'value'   => isset($row['extended']['dial_code'])? $row['extended']['dial_code'] : 'true',
												'options' => array(
													'false' 	=> __('No'),
													'true' 		=> __('Yes'),
												),
											), 'select');
										?></div>
										<div class="form-group col-md-3">
											<label for="placeholder"><?php _e('Placeholder', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Placeholder', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('Set the input\'s placeholder to an example number for the selected country, and update it if the country changes.','frontend-dashboard-extra-plus')
												));
											?></label><?php
											echo fed_input_box('placeholder', array(
												'name'    => 'extended[placeholder]',
												'value'   => isset($row['extended']['placeholder'])? $row['extended']['placeholder'] : 'polite',
												'options' => array(
													'polite' 		 => __('Polite', 'frontend-dashboard-extra-plus'),
													'aggressive' 	 => __('Aggressive', 'frontend-dashboard-extra-plus'),
													'off' 			 => __('Off', 'frontend-dashboard-extra-plus')
												),
											), 'select');
										?></div>
										<div class="form-group col-md-3">
											<label for="number_type"><?php _e('Number Type', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Number Type', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('Specify one of the keys to set the number type to use for the placeholder.','frontend-dashboard-extra-plus')
												));
											?></label><?php
											echo fed_input_box('number_type', array(
												'name' 		=> 'extended[number_type]',
												'value' 	=> isset($row['extended']['number_type'])? $row['extended']['number_type'] : 'MOBILE',
												'options' 	=> array(
													'FIXED_LINE' 				=> __('FIXED LINE', 'frontend-dashboard-extra-plus'),
													'MOBILE' 					=> __('MOBILE', 'frontend-dashboard-extra-plus'),
													'FIXED_LINE_OR_MOBILE' 		=> __('FIXED LINE OR MOBILE', 'frontend-dashboard-extra-plus'),
													'TOLL_FREE' 				=> __('TOLL FREE', 'frontend-dashboard-extra-plus'),
													'PREMIUM_RATE' 				=> __('PREMIUM RATE', 'frontend-dashboard-extra-plus'),
													'SHARED_COST' 				=> __('SHARED COST', 'frontend-dashboard-extra-plus'),
													'VOIP' 						=> __('VOIP', 'frontend-dashboard-extra-plus'),
													'PERSONAL_NUMBER' 			=> __('PERSONAL NUMBER', 'frontend-dashboard-extra-plus'),
													'PAGER' 					=> __('PAGER', 'frontend-dashboard-extra-plus'),
													'UAN' 						=> __('UAN', 'frontend-dashboard-extra-plus'),
													'VOICEMAIL' 				=> __('VOICEMAIL', 'frontend-dashboard-extra-plus'),
													'UNKNOWN' 					=> __('UNKNOWN', 'frontend-dashboard-extra-plus'),
												),
											), 'select');
										?></div>
									</div>
									<div class="row fed_intlTelInput">
										<div class="form-group col-md-3 ui-front">
											<label for="initial_country"><?php _e('Initial Country', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Initial Country', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('Set the initial country selection. You can also set it to \'Auto\', which will lookup the user\'s country based on their IP address.','frontend-dashboard-extra-plus'),
												));
											?></label><?php 
											echo fed_input_box('initial_country', array(
												'name' 		=> 'extended[initial_country]',
												'class' 	=> 'country-selector',
												'value' 	=> isset($row['extended']['initial_country'])? $row['extended']['initial_country'] : 'none',
												'options' 	=> array_merge( array(
													'null'		=> __('None', 'frontend-dashboard-extra-plus'),
													'auto' 		=> __('Auto', 'frontend-dashboard-extra-plus')
												), fed_country_iso_code()),
											), 'select');
										?></div>
										<div class="form-group col-md-3 ui-front">
											<label for="preferred_countries"><?php _e('Preferred countries', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Preferred countries', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('Specify the countries to appear at the top of the list.','frontend-dashboard-extra-plus')
												));
											?></label><?php 
											echo fed_input_box('preferred_countries', array(
												'name' 		=> 'extended[preferred_countries][]',
												'class' 	=> 'country-multiple',
												'value' 	=> isset($row['extended']['preferred_countries'])? $row['extended']['preferred_countries'] : array(),
												'options' 	=> fed_country_iso_code(),
												'extra' 		=> ' multiple=multiple ',
											), 'select');
										?></div>
										<div class="form-group col-md-3 ui-front">
											<label for="exclude_countries"><?php _e('Exclude countries', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Exclude countries', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('In the dropdown, display all countries except the ones you specify here.','frontend-dashboard-extra-plus')
												));
											?></label><?php 
											echo fed_input_box('exclude_countries', array(
												'name' 		=> 'extended[exclude_countries][]',
												'class' 	=> 'country-multiple',
												'value' 	=> isset($row['extended']['exclude_countries'])? $row['extended']['exclude_countries'] : array(),
												'options' 	=> fed_country_iso_code(),
												'extra' 		=> ' multiple=multiple ',
											), 'select');
										?></div>
										<div class="form-group col-md-3 ui-front">
											<label for="only_countries"><?php _e('Only countries', 'frontend-dashboard-extra-plus');
												echo ' '.fed_show_help_message(array(
													'title' 	=> __('Only countries', 'frontend-dashboard-extra-plus'),
													'content' 	=> __('In the dropdown, display only the countries you specify.','frontend-dashboard-extra-plus')
												));
											?></label><?php 
											echo fed_input_box('only_countries', array(
												'name' 		=> 'extended[only_countries][]',
												'class' 	=> 'country-multiple',
												'value' 	=> isset($row['extended']['only_countries'])? $row['extended']['only_countries'] : array(),
												'options' 	=> fed_country_iso_code(),
												'extra' 		=> ' multiple=multiple ',
											), 'select');
										?></div>
									</div><?php
									fed_get_admin_up_display_permission($row, $action);
									fed_get_admin_up_role_based($row, $action, $menu_options);
									fed_get_input_type_and_submit_btn('intl-tel', $action);
								?></div>
							</div>
						</div>
					</div>
				</form>
			</div><?php
		}

		/** Default extended Fields.
		 *
		 * @param $fields
		 *
		 * @return array
		 */
		public function fed_extra_plus_default_extended_fields($fields){
			return array_merge($fields, array(
				'validate' 				=> 'false',
				'dial_code' 			=> 'true',
				'placeholder' 			=> 'polite',
				'number_type' 			=> 'MOBILE',
				'initial_country' 		=> 'none',
				'preferred_countries' 	=> array(),
				'exclude_countries' 	=> array(),
				'only_countries' 		=> array(),
			));
		}

		/** Process User Profile.
		 *
		 * @param  array  $default
		 * @param  array  $row
		 * @param  string  $action
		 * @param  string  $update
		 *
		 * @return array
		 */
		public function fed_extra_plus_process_form_fields($default, $row, $action, $update){
			if( 'intl-tel' === $row['input_type'] ){
				if( 'yes' === $update ){
					$extended = array(
						'extended' => serialize(array(
							'validate' 				=> isset($row['extended']['validate'])? $row['extended']['validate'] : 'false',
							'dial_code' 			=> isset($row['extended']['dial_code'])? $row['extended']['dial_code'] : 'true',
							'placeholder' 			=> isset($row['extended']['placeholder'])? $row['extended']['placeholder'] : 'polite',
							'number_type' 			=> isset($row['extended']['number_type'])? $row['extended']['number_type'] : 'MOBILE',
							'initial_country' 		=> isset($row['extended']['initial_country'])? $row['extended']['initial_country'] : 'none',
							'preferred_countries' 	=> isset($row['extended']['preferred_countries'])? $row['extended']['preferred_countries'] : array(),
							'exclude_countries' 	=> isset($row['extended']['exclude_countries'])? $row['extended']['exclude_countries'] : array(),
							'only_countries' 	=> isset($row['extended']['only_countries'])? $row['extended']['only_countries'] : array(),
						)),
					);
					return array_merge($default, $extended);
				}else{
					$default['extended'] = $row['extended'];
					if( is_string($row['extended']) ){
						$default['extended'] = unserialize($row['extended']);
					}
				}
			}
			return $default;
		}

		/** Enqueue Scripts
		 *
		 */
		function fed_extra_plus_enqueue_script_style(){
			wp_enqueue_style('intlTelInput-css', plugins_url('assets/css/intlTelInput.min.css', FED_EXTRA_PLUS_PLUGIN));
			wp_enqueue_style('fed-extra-plus-jquery-ui', plugins_url('assets/css/jquery-ui.css', FED_EXTRA_PLUS_PLUGIN));
			wp_enqueue_style('fed-extra-plus-style', plugins_url('assets/css/style.css', FED_EXTRA_PLUS_PLUGIN));

			wp_enqueue_script('fed-extra-plus-intlTelInput', plugins_url('assets/js/intlTelInput-jquery.min.js', FED_EXTRA_PLUS_PLUGIN), array('jquery'), FED_EXTRA_PLUS_PLUGIN_VERSION);
			wp_enqueue_script('fed-extra-plus-jquery-ui-selectmenu', plugins_url('assets/js/jquery-ui.js', FED_EXTRA_PLUS_PLUGIN), array('jquery'), FED_EXTRA_PLUS_PLUGIN_VERSION);
			wp_enqueue_script('fed-extra-plus-script', plugins_url('assets/js/script.js', FED_EXTRA_PLUS_PLUGIN), array('jquery', 'fed-extra-plus-jquery-ui-selectmenu', 'fed-extra-plus-intlTelInput'), FED_EXTRA_PLUS_PLUGIN_VERSION);
			wp_localize_script('fed-extra-plus-script', 'intlTelInput', array( 
				'utils' 	=> plugins_url('assets/js/utils.js', FED_EXTRA_PLUS_PLUGIN),
				'error' 	=> array(
					__('Invalid number','frontend-dashboard-extra-plus'),
					__('Invalid country code','frontend-dashboard-extra-plus'),
					__('Too short','frontend-dashboard-extra-plus'),
					__('Too long','frontend-dashboard-extra-plus'),
					__('Invalid number','frontend-dashboard-extra-plus')
				),
			));
		}

	}
	$Fed_Extra_Plus_Menu = new Fed_Extra_Plus_Menu();
}
