<?php

/* Enqueue styles */
wp_enqueue_style('minireset', 'https://cdnjs.cloudflare.com/ajax/libs/minireset.css/0.0.2/minireset.css');
wp_enqueue_style('main', get_stylesheet_uri(), array('minireset'));

/* Enqueue scripts */
wp_enqueue_script('fontawesome', 'https://use.fontawesome.com/c137c7c541.js');

/* Register custom navigation menus */
function register_custom_menus () {
  register_nav_menu("top", __("Top Navigation"));
  register_nav_menu("sidebar_social", __("Sidebar Social Links"));
}
add_action("init", "register_custom_menus");

/* Custom navigation walker to show menu subtitles
 * Adapted from wp-includes/class-walker-nav-menu.php
 */
class Walker_Menu_Subtitles extends Walker_Nav_Menu {
  function start_el (&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $classes = empty($item->classes) ? array() : (array) $item->classes;
    $classes[] = 'menu-item-' . $item->ID;

    $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

    $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
    $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

    $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth);
    $id = $id ? ' id="' . esc_attr($id) . '"' : '';

    $output .= '<li' . $id . $class_names .'>';

    $atts = array();
    $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
    $atts['target'] = !empty($item->target)     ? $item->target     : '';
    $atts['rel']    = !empty($item->xfn)        ? $item->xfn        : '';
    $atts['href']   = !empty($item->url)        ? $item->url        : '';

    $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

    $attributes = '';
    foreach ($atts as $attr => $value) {
      if (!empty($value)) {
        $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
        $attributes .= ' ' . $attr . '="' . $value . '"';
      }
    }

    $title = apply_filters('the_title', $item->title, $item->ID);
    $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= $title;
    $item_output .= '<br><span>';
    $item_output .= $item -> description;
    $item_output .= '</span></a>';
    $item_output .= $args->after;

    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }
}

/* Shortcode for "announcement" cards */
function shortcode_announcement ($atts) {
  $color = "";
  if (!empty($atts['color'])) {
    $color = $atts['color'];
  }

  $output = "<div class='announcement " . $color . "'><header><h4>";
  $output .= $atts['title'];
  $output .= "</h4></header><p>";

  $fields = array();

  if (!empty($atts['datetime'])) {
    $fields[] = "<i class='fa fa-fw fa-lg fa-calendar-o'></i><span>"
      . $atts['datetime'] . "</span>";
  }

  if (!empty($atts['location'])) {
    $fields[] = "<i class='fa fa-fw fa-lg fa-map-marker'></i><span>"
      . $atts['location'] . "</span>";
  }

  if (!empty($atts['contact'])) {
    $fields[] = "<i class='fa fa-fw fa-lg fa-phone'></i><span>"
      . $atts['contact'] . "</span";
  }

  $output .= join("<br><br>", $fields);
  $output .= "</p></div>";

  return $output;
}
add_shortcode("announcement", "shortcode_announcement");

?>
