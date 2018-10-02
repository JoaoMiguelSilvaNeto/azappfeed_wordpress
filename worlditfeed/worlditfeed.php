<?php
/**
* Plugin Name:       azappfeed
* Description:       Obtain customized feed from WorldIT Azapp.
* Version:           1.0.0
* Author:            WorldIT
* Author URI:        https://www.worldit.pt
* Text Domain:       worldit
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* GitHub Plugin URI: https://github.com/2Fwebd/feedier-wordpress
*/

    
/*
* Plugin constants
*/
if(!defined('azappfeed_URL'))
    define('azappfeed_URL', plugin_dir_url( __FILE__ ));
if(!defined('azappfeed_PATH'))
    define('azappfeed_PATH', plugin_dir_path( __FILE__ ));
if(!defined('azappfeed_THEME_PATH'))
    define('azappfeed_THEME_PATH', get_template_directory());


/*
    * Main class
*/
    
/**
    * Class azappfeed
    *
    * This class creates the option page and add the web app script
*/
        
class azappfeed
{

            
    /**
        * azappfeed constructor.
        *
        * The main plugin actions registered for WordPress
    */
    public function __construct()
    {
        
        // Admin page calls:
        add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
        add_action( 'wp_ajax_store_admin_data', array( $this, 'storeAdminData' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'addAdminScripts' ) );
        
    }
        
    /**
         * Adds the azappfeed label to the WordPress Admin Sidebar Menu
     */
    public function addAdminMenu()
    {
        add_menu_page(
            __( 'azappfeed', 'azappfeed' ),
            __( 'azappfeed', 'azappfeed' ),
            'manage_options',
            'azappfeed',
            array($this, 'adminLayout'),
                'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAOoSURBVEiJjZZLTJxVFMd/937fzAAFy6MjU0BAZqAkVqPutFMCfWhLm4KJj4W1TUNqXFW7MWlSnxsTFzapKxN3rXFhUMujJiYWAhhs1AIRgtVmwEpptTwKNOjMfN89LmYgOMDM/Jfn/u//f+45N/dcRRrM7QtXWka1iOIQQjVQkVyaQjGphC7HVpdKvu37czMNtVFwZs9T5bbyvI1IG2ClSwIwoNrF6DeLensnMxrM793dqoQLQP7/iD4feL0ASDQKsVjq1iURdbSop69jU4P5vQ2vK5GPAL02bu18FO+BZmThHgB6m5/o55/hTk6sO40SOb21Z+D8OoNk5u2p4gDew0dwx0ZxJyIJw5ogqqAAZ2Q4lQpgRNRzKyfRALONjRXJsqwT3wi6qhpdVr7pslJy8e7+XWWrBpZ23yel5ulg1e3ACtWmoxR4XP0egJ7bF64EjmUr7nvhJaxgCOuRneS0nUxHPTHb2FhhWy6tojJexVVE27/AGRlO9GDoWjqqZVumxRbUwWzFATAGM30LlZ8PxqSlikizjSKUrbYObId4HF3xEKqwEHPnTqYtQRsIZGLJ/Bz202FkdgaMi66qxhkczCancjsbVvz7AayZGdSWxEVzIxHcG79ns9XYwDRQl4npXv81G8EUyLSNEEElDWwbb/NhZHERmZ1Fh0IoXw7m5iTuxASehkbM33/hXh/HqqtHl5YS7+1BBwKo4hJUXh6xy90gK81XEetMTVUR0Azge/5F7LodWMFadCCAufEbvqPHcUaGyD11Gnd8jNy2VzHzc+S8chx3bJScYyfQFZVovx8rVIvy+daW75x2bHUJcAH09jLiV3/A+fnHhH/JNpyRoUSWRcXELndhpm8linvzD2LfdKP8fgCcq4M4135Cl67eGcc1VqcCuNcU/hSl2qxQLblnziJLSzjDQ3jCu5H793FHhpHov3ifOYAsLxPr6iCn7SSytET0q3bsxx7HqgkisSjL776FuT0NIp8U9gy8piA5YLDHgYJsWmc/8SSe/c/yz4cfAJB39h2iX3+JO/rLCmXRE6d+S3//7dXnemHProOC7iTzBAPbgy4sxMzcBUD7H8TMz4HjABiDtBZfGeiElIGz0BQ+JUqdI8tnewMYBW9svdL/8Upg/chsajiilFwky3KtwaJW8vID3w10rQ2uy7Sop6/DihMUpc4DTjZZC1zwxKlPFYdNfhUrSLznpkWEQ2AeBpX8tsgUqIgS6XbE7ijp7Z3aTOM/ebphq7g7N8gAAAAASUVORK5CYII=
'   //sets azapp icon on the admin sidebar
        );
    }
        
    /**
     * Outputs the Admin Dashboard layout containing the form with all its options
     *
     * @return void
     */
    public function adminLayout()
    {       
        //read previously stored data
        $data = $this->getData();
        
        //store the token from thw WS (temporary, may become obsolete after creating new WS)
        $data['token'] = $this->getToken();
        
        //Obtain fonts calling the Google Fonts API
        $fonts = $this->getFonts();
        $families = '';
        
        foreach ( $fonts as $font ) { 
            
            $families .= "'" . $font['family'] . "'" . " , ";
        }
        
        //var_dump($data);

        ?>
        
        <script>

			//Load all fonts from Google, using the WebFont Loader
        	WebFont.load({
           		google: {
              		families: [<?php echo $families; ?>]
            	}
          	});
          	
        </script>
		
        <div class="wrap">
        	<img alt="azapp" style="width:5%;" src="<?php echo azappfeed_URL. '/assets/images/logo_azapp_wix.svg'?>">
    		<h3><?php _e('azappfeed API Settings', 'azappfeed'); ?></h3>

        	<p>
        		<?php _e('You can get your azappfeed API settings from your <b>Integrations</b> page.', 'azappfeed'); ?>
        	</p>

        	<hr>

        	<form id="azappfeed-admin-form">

				<table class="form-table">
                	<tbody>
                		<tr>
                        	<td scope="row">
                        		<label><h1>Azapp Credentials</h1></label>
                        	</td>
                    	<tr>
                        	<td scope="row">
                            	<label><?php _e( 'Event ID', 'azappfeed' ); ?></label>
                        	</td>
                        	<td>
                            	<input name="azappfeed_eventid"
                                   id="azappfeed_eventid"
                                   class="regular-text"
                                   value="<?php echo (isset($data['eventid'])) ? $data['eventid'] : ''; ?>"
                                />
                        	</td>
                    	</tr>
                    	<tr>
                        	<td scope="row">
                            	<label><?php _e( 'Authorization Key', 'azappfeed' ); ?></label>
                        	</td>
                        	<td>
                            	<input name="azappfeed_authkey"
                                   id="azappfeed_authkey"
                                   class="regular-text"
                                   value="<?php echo (isset($data['authkey'])) ? $data['authkey'] : ''; ?>"
                                />
                        	</td>
                    	</tr>
                    	<!-- <tr>
                        	<td scope="row">
                            	<label><?php _e( 'Feed ', 'azappfeed' ); ?></label>
                        	</td>
                        	<td>
                            	<input name="azappfeed_feed"
                                   id="azappfeed_feed"
                                   class="regular-text"
                                   value="<?php echo (isset($data['azappfeed_endpoint'])) ? $data['azappfeed_endpoint'] : ''; ?>"
                                />
                        	</td>
                    	</tr>-->
                    	<tr>
                        	<td>
                            	<hr>
                            	<h4><?php _e( 'Widget options', 'azappfeed' ); ?></h4>
                        	</td>
                    	</tr>

                    	<?php if (!empty($data['eventid']) && !empty($data['authkey'])): ?>
						
                        <?php
                        
                        // if we don't even have a response from the API
                        if (empty($data['token'])) : ?>

                            <tr>
                                <td>
                                    <p class="notice notice-error">
                                        <?php _e( 'An error happened on the WordPress side. Make sure your server allows remote calls.', 'azappfeed' ); ?>
                                    </p>
                                </td>
                            </tr>

                        <?php
                        // If we have an error returned by the API
                        elseif (isset($data['token']['error'])): ?>

                            <tr>
                                <td>
                                    <p class="notice notice-error">
                                        <?php echo $data['token']['error_description']; ?>
                                    </p>
                                </td>
                            </tr>

                        <?php
                        // If the surveys were returned
                        else: ?>

                            <tr>
                                <td>
                                    <p class="notice notice-success">
                                        <?php _e( 'The Azapp API connection is established!', 'azappfeed' ); ?>
                                    </p>
								</td>
							</tr>
							<tr>
								<td>
                                    <div>
                                        <label><?php _e( 'Choose a category', 'feedier' ); ?></label>
                                    </div>
                                    <select name="azappfeed_widget_category_id"
                                            id="azappfeed_widget_category_id">
                                        <?php
                                        
                                        $categories = $this->getCategories();
                                        
                                        foreach ($categories as $key => $value) {
                                        ?>
                                        
                                            <option value="<?php echo $value ?>" <?php echo ($value === $data['widget_category_id']) ? 'selected' : '' ?>>
                                                <?php echo $key; ?>
                                            </option>
                                               
                                        <?php     
                                        }
                                        ?>
                                    </select>
                                    <hr>
                            </tr>
							<tr>
                        		<td>
                            		<hr>
                            		<h1><?php _e( 'Widget Design options', 'azappfeed' ); ?></h1>
                            		<hr>
                        		</td>
                        		
                    		</tr>
                            <tr>
                                <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Display results (from 0 to 50)', 'azappfeed' ); ?></label>
                                    </div>
                                    <input type="range" 
                                    	id="azappfeed_widget_display_results" 
                                    	name="azappfeed_widget_display_results" 
                                    	min="5" max="50" step="5" 
                                    	value="<?php echo (isset($data['widget_display_results'])) ? $data['widget_display_results'] : '25'; ?>">
                                    	
                                    	<label id="azappfeed_widget_display_results_label"></label>
                                    	
                                    	<script>

                                    		var slider_results = document.getElementById("azappfeed_widget_display_results");
                                    		var output_results = document.getElementById("azappfeed_widget_display_results_label");
                                    		output_results.innerHTML = slider_results.value; // Display the default slider value

                                    		// Update the current slider value (each time you drag the slider handle)
                                    		slider_results.oninput = function() {
                                    	    	output_results.innerHTML = this.value;
                                    		}

                                    	</script>
                        		</td>
                        		<td>
                        			<div class="label-holder">
                                        <label><?php _e( 'Widget height', 'azappfeed' ); ?></label>
                                    </div>	
                                    <input name="azappfeed_widget_div_height"
                                           id="azappfeed_widget_div_height"
                                           class="regular-text"
                                           value="<?php echo (isset($data['widget_div_height'])) ? $data['widget_div_height'] : '100'; ?>"/>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                	<div class="label-holder">
                                        <label><h3><?php _e( 'Title Text Options', 'azappfeed' ); ?></h3></label>
                                    </div>
                                    <hr>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                	<div class="label-holder">
                                        <label><?php _e( 'Display title on top of the feed', 'azappfeed' ); ?></label>
                                    </div>
                                    <?php 
                                    
                                    if(isset($data['widget_show_title_caption']) && $data['widget_show_title_caption'] === 'yes')   {
                                    ?>    
                                        <input 	name="azappfeed_widget_show_title_caption"
                                            id="azappfeed_widget_show_title_caption"
                                            type="radio"
                                            value="yes"
                                            checked="checked">Yes
                                            
                                            <input 	name="azappfeed_widget_show_title_caption"
											id="azappfeed_widget_show_title_caption"
											type="radio" 
											value="no">No
                                            
                                    <?php     
                                    }
                                    else if(isset($data['widget_show_title_caption']) && $data['widget_show_title_caption'] === 'no')   {
                                    ?>    
                                        <input 	name="azappfeed_widget_show_title_caption"
                                            id="azappfeed_widget_show_title_caption"
                                            type="radio"
                                            value="yes">Yes
                                            
                                      	<input 	name="azappfeed_widget_show_title_caption"
											id="azappfeed_widget_show_title_caption"
											type="radio" 
											value="no"
											checked="checked">No
                                             
                                    <?php     
                                    }
                                    else    {
                                    ?>    
                                    	<input 	name="azappfeed_widget_show_title_caption" 
                                			id="azappfeed_widget_show_title_caption" 
                                			type="radio"  
                                			value="yes">Yes
										<input 	name="azappfeed_widget_show_title_caption"
											id="azappfeed_widget_show_title_caption"
											type="radio" 
											value="no">No    
                                    <?php 
                                    }
                                    ?>
                                	
                                </td>
                                <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Title caption', 'azappfeed' ); ?></label>
                                    </div>
                                    <input name="azappfeed_widget_title_caption"
                                           id="azappfeed_widget_title_caption"
                                           class="regular-text"
                                           value="<?php echo (isset($data['widget_title_caption'])) ? $data['widget_title_caption'] : 'Feed'; ?>"/>
                                </td>
                                <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Title Font', 'azappfeed' ); ?></label>
                                    </div>
                                    <select name="azappfeed_widget_title_font_selector"
                                            id="azappfeed_widget_title_font_selector" >
                                    	
                                    	<?php 
                                    	
                                    	  
                                    	foreach ( $fonts as $font ) { 
        									
        									if(isset($data['widget_title_font_selector']))	{
        										
        									    if($data['widget_title_font_selector'] === $font['family'])	{ 
        							     ?>
        									    
        											<optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
            											<option selected="selected" value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        											</optgroup> 
        								<?php 
        											
        									    }
        									    else{ 
        							     ?>
        									        
        									        <optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
        									            <option value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        											</optgroup>
        								<?php 
        									    }
        									}
        									else   {
        						        ?>
        						        
        						        			<optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
        									            <option value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        											</optgroup>            									    
        									
        								<?php     
        									}
                                    	}
        								?>                                        	
                                    </select>
                                        
                                    <script>

										jQuery("#azappfeed_widget_title_font_selector").change(function() {
				                    		var selected = jQuery("#azappfeed_widget_title_font_selector option:selected").text();
				                   	 		jQuery(this).css( 'font-family', selected );
				                		});

									</script>

                                </td>
                                <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Title font size (from 0px to 215px)', 'azappfeed' ); ?></label>
                                    </div>
                                    <input type="range" 
                                    	id="azappfeed_widget_title_font_size" 
                                    	name="azappfeed_widget_title_font_size" 
                                    	min="0" max="215" step="1" 
                                    	value="<?php echo (isset($data['widget_title_font_size'])) ? $data['widget_title_font_size'] : '31'; ?>">
                                    	
                                    	<label id="azappfeed_widget_title_font_size_label"></label>
                                    	
                                    	<script>

                                    		var slider_title_font = document.getElementById("azappfeed_widget_title_font_size");
                                    		var output_title_font = document.getElementById("azappfeed_widget_title_font_size_label");
                                    		output_title_font.innerHTML = slider_title_font.value; // Display the default slider value

                                    		// Update the current slider value (each time you drag the slider handle)
                                    		slider_title_font.oninput = function() {
                                    	    	output_title_font.innerHTML = this.value;
                                    		}

                                    	</script>
                                </td>
                                <td>
                                	<div class="label-holder">
                                        <label><?php _e( 'Title font color', 'azappfeed' ); ?></label>
                                    </div>
                                	<input type="color" 
                                		id="azappfeed_widget_title_font_color" 
                                		name="azappfeed_widget_title_font_color"  
                                		value="<?php echo (isset($data['widget_title_font_color'])) ? $data['widget_title_font_color'] : ''; ?>" />
                                </td>
                                <!-- <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Shaking effect (shake after 10s without click)', 'azappfeed' ); ?></label>
                                    </div>
                                    <input name="feedier_widget_shake"
                                           id="feedier_widget_shake"
                                           type="checkbox"
                                        <?php echo (isset($data['widget_shake']) && $data['widget_shake']) ? 'checked' : ''; ?>/>
                                </td>
                                <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Position', 'azappfeed' ); ?></label>
                                    </div>
                                    <select name="feedier_widget_position"
                                            id="feedier_widget_position">
                                        <option value="left" <?php echo (!isset($data['widget_position']) || (isset($data['widget_position']) && $data['widget_position'] === 'left')) ? 'checked' : ''; ?>>
                                            <?php _e( 'Left side', 'azappfeed' ); ?>
                                        </option>
                                        <option value="right" <?php echo (isset($data['widget_position']) && $data['widget_position'] === 'right') ? 'checked' : ''; ?>>
                                            <?php _e( 'Right side', 'azappfeed' ); ?>
                                        </option>
                                    </select>
                                </td>-->
                            </tr>
                            <tr>
                                <td>
                                	<div class="label-holder">
                                        <label><h3><?php _e( 'Description Text Options', 'azappfeed' ); ?></h3></label>
                                    </div>
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Description Font', 'azappfeed' ); ?></label>
                                    </div>
                                    <select name="azappfeed_widget_description_font_selector"
                                            id="azappfeed_widget_description_font_selector" >
                                    	
                                    	<?php 
                                    	
                                    	  
                                    	foreach ( $fonts as $font ) { 
        									
        									if(isset($data['widget_description_font_selector']))	{
        										
        									    if($data['widget_description_font_selector'] === $font['family'])	{ 
        							     ?>
        									    
        											<optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
            											<option selected="selected" value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        											</optgroup> 
        								<?php 
        											
        									    }
        									    else{ 
        							     ?>
        									        
        									        <optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
        									            <option value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        											</optgroup>
        								<?php 
        									    }
        									}
        									else   {
        									    
        								?>
        									    
        									    <optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
        									        <option value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        										</optgroup>  
        								<?php           									    
        									    
        									}
                                    	}
        								?>                                        	
                                    </select>
                                        
                                    <script>

										jQuery("#azappfeed_widget_description_font_selector").change(function() {
				                    		var selected = jQuery("#azappfeed_widget_description_font_selector option:selected").text();
				                   	 		jQuery(this).css( 'font-family', selected );
				                		});

									</script>

                                </td>
                                <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Description font size (from 0px to 215px)', 'azappfeed' ); ?></label>
                                    </div>
                                    <input type="range" 
                                    	id="azappfeed_widget_description_font_size" 
                                    	name="azappfeed_widget_description_font_size" 
                                    	min="0" max="215" step="1" 
                                    	value="<?php echo (isset($data['widget_description_font_size'])) ? $data['widget_description_font_size'] : '31'; ?>">
                                    	
                                    	<label id="azappfeed_widget_description_font_size_label"></label>
                                    	
                                    	<script>

                                    		var slider_description_font = document.getElementById("azappfeed_widget_description_font_size");
                                    		var output_description_font = document.getElementById("azappfeed_widget_description_font_size_label");
                                    		output_description_font.innerHTML = slider_description_font.value; // Display the default slider value

                                    		// Update the current slider value (each time you drag the slider handle)
                                    		slider_description_font.oninput = function() {
                                    	    	output_description_font.innerHTML = this.value;
                                    		}

                                    	</script>
                                </td>
                                <td>
                                	<div class="label-holder">
                                        <label><?php _e( 'Description font color', 'azappfeed' ); ?></label>
                                    </div>
                                	<input type="color" 
                                		id="azappfeed_widget_description_font_color" 
                                		name="azappfeed_widget_description_font_color"  
                                		value="<?php echo (isset($data['widget_description_font_color'])) ? $data['widget_description_font_color'] : ''; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                	<div class="label-holder">
                                        <label><h3><?php _e( 'Hyperlink Text Options', 'azappfeed' ); ?></h3></label>
                                    </div>
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Hyperlink Font', 'azappfeed' ); ?></label>
                                    </div>
                                    <select name="azappfeed_widget_link_font_selector"
                                            id="azappfeed_widget_link_font_selector" >
                                    	
                                    	<?php 
                                    	
                                    	  
                                    	foreach ( $fonts as $font ) { 
        									
        									if(isset($data['widget_link_font_selector']))	{
        										
        									    if($data['widget_link_font_selector'] === $font['family'])	{ 
        							     ?>
        									    
        											<optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
            											<option selected="selected" value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        											</optgroup> 
        								<?php 
        											
        									    }
        									    else{ 
        							     ?>
        									        
        									        <optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
        									            <option value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        											</optgroup>
        								<?php 
        									    }
        									}
        									else   {
        									    
        								?>
        									    
        									    <optgroup style="font-family: '<?php echo $font['family']; ?>', Arial,​ sans-serif;" data-src="http://fonts.googleapis.com/css?family=<?php echo $font['family']; ?>&text=<?php echo $font['family']; ?>">
        									        <option value="<?php echo $font['family']; ?>"><?php echo $font['family']; ?></option>
        										</optgroup>  
        								<?php           									    
        									    
        									}
                                    	}
        								?>                                        	
                                    </select>
                                        
                                    <script>

										jQuery("#azappfeed_widget_link_font_selector").change(function() {
				                    		var selected = jQuery("#azappfeed_widget_link_font_selector option:selected").text();
				                   	 		jQuery(this).css( 'font-family', selected );
				                		});

									</script>

                                </td>
                                <td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Hyperlink font size (from 0px to 215px)', 'azappfeed' ); ?></label>
                                    </div>
                                    <input type="range" 
                                    	id="azappfeed_widget_link_font_size" 
                                    	name="azappfeed_widget_link_font_size" 
                                    	min="0" max="215" step="1" 
                                    	value="<?php echo (isset($data['widget_link_font_size'])) ? $data['widget_link_font_size'] : '31'; ?>">
                                    	
                                    	<label id="azappfeed_widget_link_font_size_label"></label>
                                    	
                                    	<script>

                                    		var slider_link_font = document.getElementById("azappfeed_widget_link_font_size");
                                    		var output_link_font = document.getElementById("azappfeed_widget_link_font_size_label");
                                    		output_link_font.innerHTML = slider_link_font.value; // Display the default slider value

                                    		// Update the current slider value (each time you drag the slider handle)
                                    		slider_link_font.oninput = function() {
                                    	    	output_link_font.innerHTML = this.value;
                                    		}

                                    	</script>
                                </td>
                                <td>
                                	<div class="label-holder">
                                        <label><?php _e( 'Hyperlink font color', 'azappfeed' ); ?></label>
                                    </div>
                                	<input type="color" 
                                		id="azappfeed_widget_link_font_color" 
                                		name="azappfeed_widget_link_font_color"  
                                		value="<?php echo (isset($data['widget_link_font_color'])) ? $data['widget_link_font_color'] : ''; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                	<div class="label-holder">
                                        <label><h3><?php _e( 'Background Options', 'azappfeed' ); ?></h3></label>
                                    </div>
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                    <div class="label-holder">
                                        <label><?php _e( 'Background Opacity', 'azappfeed' ); ?></label>
                                    </div>
                                    <input type="range" 
                                    	id="azappfeed_widget_background_opacity" 
                                    	name="azappfeed_widget_background_opacity" 
                                    	min="1" max="100" step="1" 
                                    	value="<?php echo (isset($data['widget_background_opacity'])) ? $data['widget_background_opacity'] : '100'; ?>">
                                    	
                                    	<label id="azappfeed_widget_background_opacity_label"></label>
                                    	
                                    	<script>

                                    		var slider_backgound_opacity = document.getElementById("azappfeed_widget_background_opacity");
                                    		var output_backgound_opacity = document.getElementById("azappfeed_widget_background_opacity_label");
                                    		output_backgound_opacity.innerHTML = slider_backgound_opacity.value; // Display the default slider value

                                    		// Update the current slider value (each time you drag the slider handle)
                                    		slider_backgound_opacity.oninput = function() {
                                    	    	output_backgound_opacity.innerHTML = this.value;
                                    		}

                                    	</script>
                                </td> 
                                <td>
                                	<div class="label-holder">
                                        <label><?php _e( 'Background color', 'azappfeed' ); ?></label>
                                    </div>
                                	<input type="color" 
                                		id="azappfeed_widget_background_color" 
                                		name="azappfeed_widget_background_color"  
                                		value="<?php echo (isset($data['widget_background_color'])) ? $data['widget_background_color'] : ''; ?>" />
                                </td>                               
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <p class="notice notice-success">
                                        <?php _e( 'Just place the shortcode <strong>[azapp]</strong> into your page, and the Azapp Feed is ready!' ); ?>
                                    </p>
								</td>
							</tr>
                        <?php endif; ?>

                    <?php else: ?>

                        <tr>
                            <td>
                                <p>Please fill up your Azapp credentials..</p>
                            </td>
                        </tr>

                    <?php endif; ?>

                    <tr>
                        <td colspan="2">
                            <button class="button button-primary" id="azappfeed-admin-save" type="submit"><?php _e( 'Save', 'azappfeed' ); ?></button>
                        </td>
                    </tr>
                    <tr>
                    <td colspan="5"><hr></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <a href="https://www.worldit.pt" target="_blank"><img alt="worldit" src="data:image/png;base64,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
                            "></a>
                        </td>
                    </tr>
                </tbody>
            </table>

        </form>

</div>
        
        
        <?php
        
    }
        
        
    /**
     * The option name
     *
     * @var string
     */
    private $option_name = 'azappfeed_data';
        
    /**
     * Returns the saved options data as an array
     *
     * @return array
     */
    private function getData() {
        return get_option($this->option_name, array());
    }
          
    /**
     * The security nonce
     *
     * @var string
     */
    private $_nonce = 'azappfeed_admin';
        
    /**
     * Adds Admin Scripts for the Ajax call
     */
    public function addAdminScripts()
    {
        wp_enqueue_script('azappfeed-admin', azappfeed_URL. '/assets/js/admin.js', array(), 1.0);
        
        $admin_options = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            '_nonce'   => wp_create_nonce( $this->_nonce ),
        );
        
        wp_localize_script('azappfeed-admin', 'azappfeed_exchanger', $admin_options);
        
    }
        
        
    /**
    * Callback for the Token request
    *
    * Updates the options data
    *
    * @return void
    */
    public function storeAdminData()
    {
        
        if (wp_verify_nonce($_POST['security'], $this->_nonce ) === false)
            die('Invalid Request!');
            
            $data = $this->getData();
            
            foreach ($_POST as $field=>$value) {
                
                if (substr($field, 0, 10) !== "azappfeed_" || empty($value))
                    continue;
                    
                    // We remove the feedier_ prefix to clean things up
                    $field = substr($field, 10);
                    
                    $data[$field] = $value;
                    
                    
            }
            
            update_option($this->option_name, $data);
            
            /*$news = $this->getNews($data['token']);*/
            $html = '<h2 id="title" class="sample-content-title">'. $data['widget_title_caption'] .'</h2><div class="outer_div">
                <div class="inner_div"><table id="table1" class="tg">';
            
            /*foreach ($news as $entry) {
                
                $link = $entry['LINK'];
                $images_many = $entry['Images_many'];
                $title = $entry['TITLE'];
                $content = $entry['HTML_EDITOR'];
                
                $html .= '<tr>';
                $html .= '<td rowspan="2"><a href="'. $link .'"><div>';
                
                foreach ($images_many as $image)    {
                 
                    $html .= '<img style="padding:1px;" src="'. $image['IMAGE_URL'] .'" alt="'. $image['IMAGE_NAME'] .'" />';
                    
                }
                
                $html .= '</div></a></td>';
                $html .= '<td class="tg-0pky"><a class="sample-content-link" href="'. $link .'">'. $title .'</a></td></tr>';
                $html .= '<tr><td><span class="sample-content">'. $content .'</span></td></tr>';
                
                
            }*/
            
            $html .= '</table></div><div style="height:100%;"></div></div>';
            
            $this->create_theme($html);
            
            echo __('Azapp settings saved sucessfully!', 'azappfeed');
            die();
            
    }
        
    /**
     * Callback for the Google Fonts API request
     *
     * Updates the options data
     *
     * @return $data
     */
    private function getFonts() {
        
        $data = array();
        
        $api_key = 'AIzaSyDvTEjbiUQhqyY2nD5QUjTLsoNN3UUGDeA';
        
        $tokenEndpoint =  'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $api_key;
        
        $response = wp_remote_post($tokenEndpoint, array(
            'method' => 'GET',
            'headers' => array('Content-Type' => 'application/x-www-form-urlencoded')
        ));
        
        if (is_array($response) && !is_wp_error($response)) {
            $data = json_decode($response['body'], true);
            $items = $data['items'];
        }

        return $items;
        
    }
        
        
    /**
    * Callback for the Token request
    *
    * Updates the options data
    *
    * @return $data
    */
    private function getToken() {
        
        $data = array();
        
        $tokenEndpoint =  'https://azapp-services.azurewebsites.net/api/security/token';
        
        $response = wp_remote_post($tokenEndpoint, array(
            'method' => 'POST',
            'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
            'body' => array( 'grant_type' => 'password', 'username' => 'volta18tvOS', 'password' => 'volta18#tvOS')
        ));
        
        if (is_array($response) && !is_wp_error($response)) {
            $data = json_decode($response['body'], true);
            
        }
        
        return $data;
        
    }
        
    /**
     * Callback for the Feed request
     *
     * Updates the options data
     *
     * @return $news
     */
    /*private function getNews($token) {
        
        $news = array();
        
        $newsEndpoint =  $data['endpoint'];
        
        $response = wp_remote_get($newsEndpoint, array(
            'method' => 'GET',
            'headers' => array('Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/x-www-form-urlencoded'),
            'body' => array(
                'page' => '0',
                'records' => '30',
                'date' => '2018-08-11T15:48:30.743Z',
                'id' => '5',
                'lang' => 'PT',
                'theme' => 'Noticias')
        ));
        
        if (is_array($response) && !is_wp_error($response)) {
            $data = json_decode($response['body'], true);
        }
        
        print_r($news);
        return $news;
        
    }*/
    
    /**
     * Receives categories from Azapp User 
     *
     * Should receive categories available for Azapp user (WS) and return
     * them to populate dropdown list in admin page.
     *
     * @return $categories
     */
    private function getCategories() {
        
        $categories = array();
        
        $categories['Notícias'] = 'Noticias';
        $categories['Informações'] = 'Informacoes';
        $categories['Contactos'] = 'Contactos';
        
        return $categories;
    }
     
    /**
     * Convert color hex strings into rgba
     *
     * Converts the given color and opacity into rgba
     *
     * @return $output
     */
    private function hex2rgba($color, $opacity) {
        
        $default = 'rgb(0,0,0)';
        
        if (empty($color))
            return $default;
            
        if ($color[0] == '#')
            $color = substr($color, 1);
                
        if (strlen($color) == 6)
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
                    
        elseif (strlen($color) == 3)
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
                    
        else
            return $default;
                        
        $rgb = array_map('hexdec', $hex);
                        
            if ($opacity) {
                if (abs($opacity) > 1)
                    $opacity = 1.0; //opacity values never goes beyond 1 (100%)
                                
                $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
            }
            else {
                            
                $output = 'rgba(' . implode(",", $rgb) . ',0)';
            }
            
            return $output;
    }
    
    /**
     * Creates Azapp Wordpress Theme
     *
     * Creates the wordpress dependent azapp theme files, based on the user data
     *
     * @return void
     */
        
    private function create_theme($content)  {
            
        
        $theme = wp_get_theme();    //Get current theme info from wordpress API
        $dir = get_theme_root() . "/azapp"; //Get current directory of azapp theme
        $parent_style = '$parent_style';    //to print in functions.php
        $params = '$params';    //to print in funtions.php
        $data = $this->getData();   //return stored data
        
        //Set screenshot image for wordpress themes page
        $screenshot = 'data:image/png;base64,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SwH0UJ2EiIjUEfE4jJ69YPToAWPH7hCxGERZOUQ8Dpim6njUDrK+vmmTl0QC9upVsJcuRW7pYsi1a1VHIyIifSy2wOMjiIhKnmxsRG7BfOQWzFcdhYiIiNyXMwCkVacgIiIiIiIiz2QMADwkiIiIiIiIqHQ0shAkIiIiIiIqIRJIGIBsVB2EiIiIiIiIvCEkGg1AcESQiIiIiIioRAghEgYAjggSERERERGVCClloyGEYCFIRERERERUKgQaDWnbnBpKRERERERUKoRIcESQiIiIiIiolEjZaEjJ4yOIiIiIiIhKhRRIGEJw11AiIiIiIqJSIWwkDBuyXnUQIiIiIiIi8oaAbDAAuV51ECIiIiIiIvKGDdQaAGpVByEiIiIiIiKvGLWGgMERQSIiIiIiolIhUGsAkiOCREREREREJUIKWWvAYCFIRERERERUMoSsNYSZYyFIRERERERUImQoU2t06NBjPQCpOgwRERERERG5Llf90jt1hnj66RwAniVIREREREQUfOsFIA0AgOQREkRERERERCWgFgCaCkHBQpCIiIiIiKgErAc2FoKCR0gQERERERGVAPnDiKAU4nu1YYiIiIiIiMh94jtg09RQCRaCREREREREAbdpELBpRBAcESQiIiIiIgo6Ie0fCkFj42+IiIiIiIgoyLYeEfxObRgiIiIiIiJym9y4LNAAANswOCJIREREREQUcObG2s8AAMvOshAkIiIiIiIKOFtmfigEczLEqaFEREREREQBl0k1rREUm/6gdsLYJICIskRERERERETkpkTV6zPjwKZzBJusUhSGiIiIiIiI3Ld5SeCWhSCnhxIREREREQVX80JQCCxTk4WIiIiIiIjcJ5du+rfNhaBtSxaCREREREREASWA5oWgEAYLQSIiIiIiooCS+KHm23Jq6NKWX05ERERERES+J1oYEeTUUCIiIiIiouAyZEtTQ03JEUEiIiIiIqKAyhp286mhlaGK5QCkkkRERERERETkplzHdYlvN/3mhxHBl15KgYfKExERERERBZBYKebOzWz6nbHN33KdIBERERERUdBssznoVoUgdw4lIiIiIiIKINlGIWhLFoJERERERERBI7D1KRFbTw2V+MbTNEREREREROSFrWq9bdYIiq+9TEJEREREREResLeq9bYqBKU0OCJIREREREQUMEKI1gvBZEXdN+BZgkREREREREFiV4QrFm/5B1sVgjs+N7dRCqz0NhMRERERERG5aOnGc+M32/YcQRg2uE6QiIiIiIgoOJrVeM0KwZZeRERERERERL7FQpCIiIiIiKi0iGabgrZQCPIICSIiIiIiouCw8xkRbP4iIiIiIiIi8qvmg33NCkFhRL7yJgwRERERERG5zTYizQpB0dILayeM/R5AZ9cTERERERERkWsEsKLy9Zndt/3zljaLAYDPXc5DRERERERELpOt1HatFYKfuZiFiIiIiIiIPCBbqe1aLASFlBwRJCIiIiIi8jnRnhFBaZgsBImIiIiIiHxOGi0P8rVYCOZygoUgERERERGRz4VkpsXarsVdQwGgdsLYWgCVriUiIiIiIiIiN62ren1mdUt/0dpmMQCwwKUwRERERERE5LLWNooB2igEBXcOJSIiIiIi8q22arpWC0EpeJYgERERERGRX0nZek3X+oigjXnuxCEiIiIiIiK3GYWMCGal+ZE7cYiIiIiIiMhtppH+sLW/a3XXUAConTB2FYBOjiciIiIiIiIiN62sen3mDq39ZVu7hgISrVaQREREREREpKnt1HJtF4KChSAREREREZHvCFl4ISgEuE6QiIiIiIjIZ4QQbdZybRaCue18MREREREREenHKKYQ7Jg1PgeQdDQRERERERERuSlR3rHrF229oO2podOnZwGeJ0hEREREROQjn4inn8619YK2N4sBANn2IkMiIiIiIiLSiBDbreG2Wwhub5EhERERERER6UPY9nZruO0XghLvOxOHiIiIiIiI3CYM84PtvWa7heD6ZPZ9ABlHEhEREREREZGbMusb08VPDe05e3YCwKeORCIiIiIiIiL3CPHRxhquTdvfLAYApHy36EBERERERETkKmnb7+TzurwKQQmwECQiIiIiItJfXrWblc+LpLTeFaLNYyiIvBEOw+zTF8aO3Zt+de4MUV0NUVUFEY5ARCJAOAzZ2Agkk5CJBGRDPWRdHeylS5Fbshj20iWwv1sJ2Lbq74aItmSFYPbp0/Te7t4dRucuEB2rITp2bHpvh8IQ0QhkMgkkEpDJBGRdHWR9Pezly2AvWdL0Hl/5LZDjzyz6gaishNmrN4xevWH06AmjUycgFoOIxiDKyiDicQBo+tmx8Z9y7RrI2lrY69bCXras6RpbtgwylVT5rZDDRFVV07XR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
';
        list($type, $screenshot) = explode(';', $screenshot);
        list(, $screenshot)      = explode(',', $screenshot);
        $screenshot = base64_decode($screenshot);
        
        //Compensate bottom of plugin scroll
        $inner_div_height = intval($data['widget_div_height']) - 15;
        
        //set opacity in integer value
        $opacity = ((int)($data['widget_background_opacity']))/100;
        $rgba = $this->hex2rgba($data['widget_background_color'], $opacity);    //send to convert in rgba
            
        //Get current theme stylesheet name, to apply to function.php
        if($theme->parent_theme === '')    {
                
            $parent = $theme->get_stylesheet();
            $parent_name = $theme->get('Name');
                
        }
        else {
                
            $parent = str_replace(' ', '', strtolower ($theme->parent_theme));
            $parent_name = $theme->parent_theme;
                
        }
          
        //Get Google Fonts
        $fonts = $this->getFonts();
            
        foreach ( $fonts as $font ) {
                
            $families .= "'" . $font['family'] . "'" . " , ";
        }
            
        //Set azapp's theme new functions.php
        $functions_php = 
            "<?php\n" .
            "/**\n" .
            " * Azapp Child functions and definitions\n" .
            " * @link https://developer.wordpress.org/themes/basics/theme-functions/\n" .
            " *\n"  .
            " * @package WordPress\n"   .
            " * @subpackage ". $parent ."\n" .
            " * @since 1.0\n"   .
            " */\n" .
            "\n"    .
            "function azapp_enqueue_styles() {\n"   .
            "\n"    .
            "\t". $parent_style ." = '". $parent ."-style';\n"    .
            "\n"    .
            "\twp_enqueue_style( ". $parent_style .", get_template_directory_uri() . '/style.css' );\n" .
            "\twp_enqueue_style( 'child-style',\n"  .
            "\t\tget_stylesheet_directory_uri() . '/style.css',\n"  .
            "\t\tarray( ". $parent_style. " ),\n" .
            "\t\twp_get_theme()->get('Version')\n"  .
            "\t);\n"    .
            "}\n"   .
            "add_action( 'wp_enqueue_scripts', 'azapp_enqueue_styles' );\n" .
            "\n"    .
            "// Azapp Shortcode Function to Include\n"  .
            "function azapp_shortcode() {\n"    .
            "\t return '" . $content . "';\n"    .
            "}\n"   .
            "add_shortcode( 'azapp', 'azapp_shortcode' );\n"    .
            "\n" .
            "//Add jquery\n"    .
            "function insert_jquery(){\n"   .
            "\twp_enqueue_script('jquery', plugin_dir_url( 'worlditfeed.php' ) . 'worlditfeed/assets/js/jquery.min.js', array(), '1.1', false);\n"    .
            "}\n"   .
            "add_filter('admin_enqueue_scripts','insert_jquery',1);\n"  .
            "\n"    .
            "//Add Google Web Font Loader script for admin\n" .
            "function insert_webfontloader_script_admin(){\n"    .
            "\twp_enqueue_script('webfontloader', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array('jquery'), '1.1', false);\n"   .
            "}\n"   .
            "add_filter('admin_enqueue_scripts','insert_webfontloader_script_admin',1);\n"    .
            "\n"    .
            "//Add Google Web Font Loader script for WP\n" .
            "function insert_webfontloader_script_wp(){\n"    .
            "\twp_enqueue_script('webfontloader', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array('jquery'), '1.1', false);\n"   .
            "}\n"   .
            "add_filter('wp_enqueue_scripts','insert_webfontloader_script_wp',1);\n"    .
            "\n"    .
            "//Add feed\n"  .
            "\n"    .
            "$params = array(\n"  .
            "\t'category' => " . $data['widget_category_id'] . ",\n" .
            "\t'results'  => " . $data['widget_display_results'] . ",\n".
            "\t'font_families'  => [" . $families. "]\n".
            ");\n"    .
            "\n" .
            "wp_enqueue_script('feed', plugin_dir_url( 'worlditfeed.php' ) . 'worlditfeed/assets/js/feed.js', array('jquery'), '1.1', false);\n"    .
            "wp_localize_script( 'feed', 'Feed', $params );\n";
            
        //Set new azapps's theme new stylesheet
        $styles_css = 
            "/*\n"  .
            "\tTheme Name:   Azapp Theme\n" .
            "\tTheme URI:    http://example.com/azapp-theme\n" .
            "\tDescription:  ". $parent_name ." Child Theme, in order to use the Azapp Feed inside your pages.\n"   .
            "\tAuthor:       WorldIT\n" .
            "\tAuthor URI:   http://worldit.pt\n" .
            "\tTemplate:     ". $parent ."\n"    .
            "\tVersion:      1.0.0\n"   .
            "\tLicense:      GNU General Public License v2 or later\n"  .
            "\tLicense URI:  http://www.gnu.org/licenses/gpl-2.0.html\n"    .
            "\tTags:         light, dark, two-columns, right-sidebar, responsive-layout, accessibility-ready\n" .
            "\tText Domain:  azapp\n"   .
            "*/\n"    .
            "\n"    .
            ".tg\n" .
            "{\n"   .
            "\tborder-collapse:collapse;\n" .
            "\tborder-spacing:0;\n" .
            "\toverflow-y: auto;\n" .
            "\ttable-layout:fixed;\n"   .
            "}\n"   .
            "\n"    .
            ".tg td\n"  .
            "{\n"   .
            "\tfont-family:". $data['widget_description_font_selector'] .";\n"    .
            "\tfont-size:". $data['widget_description_font_size'] ."px;\n"    .
            "\tcolor:". $data['widget_description_font_color'] .";\n"  .
            "\tpadding:10px 5px;\n" .
            "\tborder-style:solid;\n" .
            "\tborder-width:1px;\n" .
            "\toverflow:hidden;\n"  .
            "\tword-break:normal;\n"    .
            "\tborder-color:black;\n"   .
            "\tborder-right: none;\n"   .
            "\tborder-left:none;\n"     .
            "\tvertical-align:top;\n"   .
            "}\n"   .
            "\n"    .
            ".tg td div\n"  .
            "{\n"   .
            "\tborder: 1px hidden gray;\n"  .
            "\theight: inherit;\n"  .
            "\twidth: inherit;\n"   .
            "\toverflow: hidden;\n" .
            "\tbox-sizing: border-box;\n"   .
            "}\n"   .
            "\n"    .
            ".tg td div img\n"  .
            "{\n"   .
            "\tdisplay:block;\n"   .
            "}\n"   .
            "\n"    .
            ".tg th\n"  .
            "{\n"   .
            "\tfont-family:Arial, sans-serif;\n"    .
            "\tfont-size:9px;\n"    .
            "\tfont-weight:normal;\n"   .
            "\tpadding:10px 5px;\n" .
            "\tborder-style:solid;\n"   .
            "\tborder-width:1px;\n" .
            "\toverflow:hidden;\n"  .
            "\tword-break:normal;\n"    .
            "\tborder-color:black;\n"   .
            "\tborder-left: 0px solid;\n"   .
            "\tborder-right: 0px solid;\n"  .
            "}\n"   .
            "\n"    .
            ".tg .tg-0pky\n"    .
            "{\n"   .
            "\tborder-color:inherit;\n" .
            "\ttext-align:left;\n"  .
            "\tvertical-align:top;\n"    .
            "\twidth:60%;\n"    .
            "}\n"   .
            "\n"    .
            ".tg td a\n"  .
            "{\n"   .
            "\tfont-family:". $data['widget_link_font_selector'] .";\n"    .
            "\tfont-size:". $data['widget_link_font_size'] .";\n"   .
            "\tcolor:". $data['widget_link_font_color'] .";\n"  .
            "\ttext-decoration:none;\n"    .
            "}\n"   .
            ".sample-content-title\n"   .
            "{\n" .
            "\tfont-family:". $data['widget_title_font_selector'] .";\n"    .
            "\tfont-size:". $data['widget_title_font_size'] .";\n"  .
            "\tcolor:". $data['widget_title_font_color'] .";\n" .
            "}\n"   .
            ".outer_div\n"  .
            "{\n"   .
            "\tbackground-color:". $rgba .";\n"  .
            "\tdisplay:inline-block;\n" .
            "\theight:". $data['widget_div_height'] ."vh;\n"  .
            "}\n"   .
            "\n"    .
            ".inner_div\n"  .
            "{\n"   .
            "\toverflow-y:auto;\n"  .
            "\theight:". $inner_div_height ."vh;\n"    .
            "}\n"   .
            "\n"    .
            "/* Scroll Width */\n"    .
            "::-webkit-scrollbar\n"   .
            "{\n" .
            "\twidth: 5px;\n" .
            "}\n" .
            "\n"    .
            "/* Scroll Track */\n"    .
            "::-webkit-scrollbar-track\n" .
            "{\n" .
            "\tbox-shadow: inset 0 0 5px grey;\n" .
            "\tborder-radius: 10px;\n"    .
            "}\n"   .
            "\n"    .
            "/* Scroll Handle */\n" .
            "::-webkit-scrollbar-thumb\n"   .
            "{\n"   .
            "\tbackground: grey;\n" .
            "\tborder-radius: 10px;\n"  .
            "}\n"   .
            "\n"    .
            "/* Scroll Handle on hover */\n"    .
            "::-webkit-scrollbar-thumb:hover\n" .
            "{\n"   .
            "\tbackground: black;\n"  .
            "}\n"   .
            "\n";
            
        //Save new files:
        //Remove child theme folder, if exists
        if(file_exists ( $dir ))    {
            
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
            
            foreach($files as $file) {
                
                if ($file->isDir()){
                    
                    rmdir($file->getRealPath());
                    
                } 
                else {
                    
                    unlink($file->getRealPath());
                    
                }
            }
            
            //Save new files
            file_put_contents(get_theme_root() . '/azapp/functions.php', $functions_php.PHP_EOL , FILE_APPEND | LOCK_EX);
            file_put_contents(get_theme_root() . '/azapp/style.css', $styles_css.PHP_EOL , FILE_APPEND | LOCK_EX);
            
            file_put_contents(get_theme_root() . '/azapp/screenshot.png', $screenshot);
            
        }
        else {
            
            //If it doesn't exist, create azapp theme folder
            mkdir(get_theme_root() . "/azapp");
            
            //Save new files
            file_put_contents(get_theme_root() . '/azapp/functions.php', $functions_php.PHP_EOL , FILE_APPEND | LOCK_EX);
            file_put_contents(get_theme_root() . '/azapp/style.css', $styles_css.PHP_EOL , FILE_APPEND | LOCK_EX);
            
            file_put_contents(get_theme_root() . '/azapp/screenshot.png', $screenshot);
            
        }
            //Automatically switch to new azapp theme, that will be dependent on previous parent theme.
            switch_theme( $parent, 'azapp' );

        }
        
            
}
    
new azappfeed();
    
 
?>