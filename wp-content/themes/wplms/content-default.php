<?php
/**
 * BLOG 1 Style Content Block
 */

if ( ! defined( 'ABSPATH' ) ) exit;

echo ' <div class="blogpost">
        <div class="meta">
           <div class="date">
            <p class="day"><span>'.sprintf('%02d', get_the_time('j')).'</span></p>
            <p class="month">'.get_the_time('M').'\''.get_the_time('y').'</p>
           </div>
        </div>
        '.(has_post_thumbnail(get_the_ID())?'
        <div class="featured">
            <a href="'.get_permalink().'">'.get_the_post_thumbnail(get_the_ID(),'full').'</a>
        </div>':'').'
        <div class="excerpt '.(has_post_thumbnail(get_the_ID())?'thumb':'').'">
            <h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>
            <div class="cats">
                '.$cats.'
                <p>
                <a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.get_the_author_meta( 'display_name' ).'</a>
                </p>
            </div>
            <p>'.get_the_excerpt().'</p>
            <a href="'.get_permalink().'" class="link">'.__('Read More','vibe').'</a>
        </div>
    </div>';