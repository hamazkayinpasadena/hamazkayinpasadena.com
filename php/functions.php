<?php

/* Enqueue styles */
wp_enqueue_style('minireset', 'https://cdnjs.cloudflare.com/ajax/libs/minireset.css/0.0.2/minireset.css');
wp_enqueue_style('main', get_stylesheet_uri(), array('minireset'));

/* Enqueue scripts */
wp_enqueue_script('fontawesome', 'https://use.fontawesome.com/c137c7c541.js');

/* Register custom navigation menus */
function register_custom_menus () {
  register_nav_menu('primary', __('Primary Navigation'));
  register_nav_menu('booklist', __('Book List'));
}
add_action('init', 'register_custom_menus');

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
    $item_output .= '<a'. $attributes .'><span class="am bold">';
    $item_output .= $title;
    $item_output .= '</span><br><span class="subtitle">';
    $item_output .= $item -> description;
    $item_output .= '</span></a>';
    $item_output .= $args->after;

    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }
}

function is_parent_page () {
  global $post;
  return count(get_pages(array('child_of' => $post->ID))) > 0;
}

function show_post_grid ($atts) {
  $a = shortcode_atts(array(
    'rows' => 2,
    'cols' => 2,
    'tag' => 'featured',
  ), $atts);

  // min columns = 2, max columns = 4
  if ($a['cols'] < 2) $a['cols'] = 2;
  if ($a['cols'] > 4) $a['cols'] = 4;

  // min rows = 1, max rows = 4
  if ($a['rows'] < 1) $a['rows'] = 1;
  if ($a['rows'] > 4) $a['rows'] = 4;

  $ret = "";

  global $post;
  $query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => $a['rows'] * $a['cols'],
    'tag' => $a['tag'],
  ));

  $ret .= '<section class="featured_posts cols' . $a['cols'] . '">';

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();

      $image = get_the_post_thumbnail_url(get_the_ID(), 'large');
      $title = the_title('<h3>', '</h3>', false);
      $desc = get_the_content();
      $link_url = get_the_permalink();
      $link_text = get_post_meta(get_the_ID(), 'featured_button_text', true);

      // Build the article
      $ret .= '<a class="featured" href="' . $link_url . '">';
      $ret .= '<article class="featured">';
      $ret .= '<header style="background-image: url(' . $image . ')"></header>';
      $ret .= $title;
      /* $ret .= '<p>' . $desc . '</p>'; */
      $ret .= '</article>';
      $ret .= '</a>';
    }

    wp_reset_postdata();
  }

  $ret .= '</section>';

  return $ret;
}
add_shortcode('post_grid', 'show_post_grid');

?>
