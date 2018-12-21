<?php
/**
 * @package   Disable_WYSIWYG
 * @author    Tunghsiao Liu <t@sparanoid.com>
 * @license   GPL-2.0+
 * @link      https://sparanoid.com/
 * @copyright Sparanoid
 *
 * @wordpress-plugin
 * Plugin Name:       Disable WYSIWYG
 * Plugin URI:        https://sparanoid.com/work/disable-wysiwyg/
 * Description:       Disable TinyMCE Visual Editor (WYSIWYG editor) totally completely permanently forever
 * Version:           1.0.8
 * Author:            Tunghsiao Liu
 * Author URI:        https://sparanoid.com/
 * Text Domain:       disable-wysiwyg
 * License:           GPL2+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/sparanoid/disable-wysiwyg
 */

/**
 * Disable WYSIWYG
 *
 * @since Disable_WYSIWYG 1.0.0
 */

add_filter( 'user_can_richedit', '__return_false' );
