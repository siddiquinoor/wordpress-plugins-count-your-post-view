<?php
/*
  Plugin Name: Count Your Post View
  Plugin URI: http://unicornit.net/
  Description: This plugin will help you to count how many times a post has been viewed and save data into your database. You have to show the count of total views by following a very simple steps. Please read carefully the attached readme file.
  Version: 1.0
  Author: Siddiqui Noor
  Author URI: http://siddiquinoor.com/
  License: GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/*
  Copyright 2014-2015  Siddiqui Noor  (email : mr.noorbd@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

register_activation_hook(__FILE__, uiton_install());
Register_uninstall_hook(__FILE__, uiton_drop());

function uiton_install() {
    global $wpdb;
    $table = $wpdb->prefix . "uitinviews";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
        $sql = "CREATE TABLE " . $table .
                " ( UNIQUE KEY id (post_id), post_id int(10) NOT NULL,
             view int(10),
            view_datetime datetime NOT NULL default '0000-00-00 00:00:00');";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function uiton_drop() {
    global $wpdb;
    $table = $wpdb->prefix . "uitinviews";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
        $sql = "DROP TABLE " . $table;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

if (!function_exists('fetch_views')) {
    function fetch_views($post_id) {
        if (views_update($post_id) == 1) {
            $views = views_get($post_id);
            echo number_format_i18n($views);
        } else {
            echo 0;
        }
    }
}

function views_add($view, $post_id) {
    global $wpdb;
    $table = $wpdb->prefix . "uitinviews";
    $data = $wpdb->query("INSERT INTO $table VALUES($post_id,$view,NOW())");
    return $data;
}

function views_update($post_id) {
    global $wpdb;
    $table = $wpdb->prefix . "uitinviews";
    $views = views_get($post_id) + 1;
    if ($wpdb->query("SELECT view FROM $table WHERE post_id = '$post_id'") != 1)
        views_add($views, $post_id);
    $data = $wpdb->query("UPDATE $table SET view = $views WHERE post_id = '$post_id'");
    return $data;
}

function views_get($post_id) {
    global $wpdb;
    $table = $wpdb->prefix . "uitinviews";
    $data = $wpdb->get_results("SELECT view FROM $table WHERE post_id = '$post_id'", ARRAY_A);
    if (!is_array($data) || empty($data)) {
        return "0";
    } else {
        return $data[0]['view'];
    }
}

?>