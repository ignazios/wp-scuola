<?php
/**
 * Post API: Walker_Category class Personalizzata
 *
 * @see Walker
 */
 class My_Category_Walker extends Walker_Category {

 public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$cat_name = esc_attr( $category->name );
		if ( '' === $cat_name ) {
			return;
		}
		$atts         = array();
		$atts['href'] = get_term_link( $category );
		if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
			$atts['title'] = strip_tags( apply_filters( 'category_description', $category->description, $category ) );
		}
		$atts = apply_filters( 'category_list_link_attributes', $atts, $category, $depth, $args, $id );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
        if(!empty($args["link_before"])) $cat_name=$args["link_before"].$cat_name;
        if(!empty($args["link_after"])) $cat_name=$cat_name.$args["link_after"];
        if(!empty( $args['show_count']) and !empty($args["count_before"])) $cat_name=$cat_name.$args["count_before"].number_format_i18n( $category->count );
        if(!empty( $args['show_count']) and !empty($args["count_after"])) $cat_name=$cat_name.$args["count_after"];
        if(!empty($args["link_close"])) $cat_name=$cat_name.$args["link_close"];

	 		$link = sprintf(
			'<a%s>%s</a>',
			$attributes,
			$cat_name);

		if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
			$link .= ' ';

			if ( empty( $args['feed_image'] ) ) {
				$link .= '(';
			}

			$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $args['feed_type'] ) ) . '"';

			if ( empty( $args['feed'] ) ) {
				/* translators: %s: Category name. */
				$alt = ' alt="' . sprintf( __( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
			} else {
				$alt   = ' alt="' . $args['feed'] . '"';
				$name  = $args['feed'];
				$link .= empty( $args['title'] ) ? '' : $args['title'];
			}

			$link .= '>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= $name;
			} else {
				$link .= "<img src='" . esc_url( $args['feed_image'] ) . "'$alt" . ' />';
			}
			$link .= '</a>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= ')';
			}
		}

		if ( 'list' == $args['style'] ) {
			$output     .= "\t<li";
			$css_classes = array(
				'cat-item',
				'cat-item-' . $category->term_id,
			);

			if ( ! empty( $args['current_category'] ) ) {
				// 'current_category' can be an array, so we use `get_terms()`.
				$_current_terms = get_terms(
					array(
						'taxonomy'   => $category->taxonomy,
						'include'    => $args['current_category'],
						'hide_empty' => false,
					)
				);

				foreach ( $_current_terms as $_current_term ) {
					if ( $category->term_id == $_current_term->term_id ) {
						$css_classes[] = 'current-cat';
						$link          = str_replace( '<a', '<a aria-current="page"', $link );
					} elseif ( $category->term_id == $_current_term->parent ) {
						$css_classes[] = 'current-cat-parent';
					}
					while ( $_current_term->parent ) {
						if ( $category->term_id == $_current_term->parent ) {
							$css_classes[] = 'current-cat-ancestor';
							break;
						}
						$_current_term = get_term( $_current_term->parent, $category->taxonomy );
					}
				}
			}

			/**
			 * Filters the list of CSS classes to include with each category in the list.
			 *
			 * @since 4.2.0
			 *
			 * @see wp_list_categories()
			 *
			 * @param array  $css_classes An array of CSS classes to be applied to each list item.
			 * @param object $category    Category data object.
			 * @param int    $depth       Depth of page, used for padding.
			 * @param array  $args        An array of wp_list_categories() arguments.
			 */
			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );
			$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

			$output .= $css_classes;
			$output .= ">$link\n";
		} elseif ( isset( $args['separator'] ) ) {
			$output .= "\t$link" . $args['separator'] . "\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}

}
function my_wp_list_categories( $args = '' ) {
    $defaults = array(
        'child_of'            => 0,
        'current_category'    => 0,
        'depth'               => 0,
        'echo'                => 1,
        'exclude'             => '',
        'exclude_tree'        => '',
        'feed'                => '',
        'feed_image'          => '',
        'feed_type'           => '',
        'hide_empty'          => 1,
        'hide_title_if_empty' => false,
        'hierarchical'        => true,
        'order'               => 'ASC',
        'orderby'             => 'name',
        'separator'           => '<br />',
        'show_count'          => 0,
        'show_option_all'     => '',
        'show_option_none'    => __( 'No categories' ),
        'style'               => 'list',
        'taxonomy'            => 'category',
        'title_li'            => __( 'Categories' ),
        'use_desc_for_title'  => 1,
    );
 
    $parsed_args = wp_parse_args( $args, $defaults );
 
    if ( ! isset( $parsed_args['pad_counts'] ) && $parsed_args['show_count'] && $parsed_args['hierarchical'] ) {
        $parsed_args['pad_counts'] = true;
    }
 
    // Descendants of exclusions should be excluded too.
    if ( true == $parsed_args['hierarchical'] ) {
        $exclude_tree = array();
 
        if ( $parsed_args['exclude_tree'] ) {
            $exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $parsed_args['exclude_tree'] ) );
        }
 
        if ( $parsed_args['exclude'] ) {
            $exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $parsed_args['exclude'] ) );
        }
 
        $parsed_args['exclude_tree'] = $exclude_tree;
        $parsed_args['exclude']      = '';
    }
 
    if ( ! isset( $parsed_args['class'] ) ) {
        $parsed_args['class'] = ( 'category' == $parsed_args['taxonomy'] ) ? 'categories' : $parsed_args['taxonomy'];
    }
 
    if ( ! taxonomy_exists( $parsed_args['taxonomy'] ) ) {
        return false;
    }
 
    $show_option_all  = $parsed_args['show_option_all'];
    $show_option_none = $parsed_args['show_option_none'];
 
    $categories = get_categories( $parsed_args );
 
    $output = '';
    if ( $parsed_args['title_li'] && 'list' == $parsed_args['style'] && ( ! empty( $categories ) || ! $parsed_args['hide_title_if_empty'] ) ) {
        $output = '<li class="' . esc_attr( $parsed_args['class'] ) . '">' . $parsed_args['title_li'] . '<ul>';
    }
    if ( empty( $categories ) ) {
        if ( ! empty( $show_option_none ) ) {
            if ( 'list' == $parsed_args['style'] ) {
                $output .= '<li class="cat-item-none">' . $show_option_none . '</li>';
            } else {
                $output .= $show_option_none;
            }
        }
    } else {
        if ( ! empty( $show_option_all ) ) {
 
            $posts_page = '';
 
            // For taxonomies that belong only to custom post types, point to a valid archive.
            $taxonomy_object = get_taxonomy( $parsed_args['taxonomy'] );
            if ( ! in_array( 'post', $taxonomy_object->object_type ) && ! in_array( 'page', $taxonomy_object->object_type ) ) {
                foreach ( $taxonomy_object->object_type as $object_type ) {
                    $_object_type = get_post_type_object( $object_type );
 
                    // Grab the first one.
                    if ( ! empty( $_object_type->has_archive ) ) {
                        $posts_page = get_post_type_archive_link( $object_type );
                        break;
                    }
                }
            }
 
            // Fallback for the 'All' link is the posts page.
            if ( ! $posts_page ) {
                if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) {
                    $posts_page = get_permalink( get_option( 'page_for_posts' ) );
                } else {
                    $posts_page = home_url( '/' );
                }
            }
 
            $posts_page = esc_url( $posts_page );
            if ( 'list' == $parsed_args['style'] ) {
                $output .= "<li class='cat-item-all'><a href='$posts_page'>$show_option_all</a></li>";
            } else {
                $output .= "<a href='$posts_page'>$show_option_all</a>";
            }
        }
 
        if ( empty( $parsed_args['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
            $current_term_object = get_queried_object();
            if ( $current_term_object && $parsed_args['taxonomy'] === $current_term_object->taxonomy ) {
                $parsed_args['current_category'] = get_queried_object_id();
            }
        }
 
        if ( $parsed_args['hierarchical'] ) {
            $depth = $parsed_args['depth'];
        } else {
            $depth = -1; // Flat.
        }
        $walker = new My_Category_Walker;
        $output .= $walker->walk( $categories, $depth, $parsed_args );
    }
 
    if ( $parsed_args['title_li'] && 'list' == $parsed_args['style'] && ( ! empty( $categories ) || ! $parsed_args['hide_title_if_empty'] ) ) {
        $output .= '</ul></li>';
    }
 
    /**
     * Filters the HTML output of a taxonomy list.
     *
     * @since 2.1.0
     *
     * @param string $output HTML output.
     * @param array  $args   An array of taxonomy-listing arguments.
     */
    $html = apply_filters( 'wp_list_categories', $output, $args );
 
    if ( $parsed_args['echo'] ) {
        echo $html;
    } else {
        return $html;
    }
}
?>