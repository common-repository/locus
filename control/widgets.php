<?php


/**
 * PPWidget Class
 */
class PPWidget extends WP_Widget {

    /** constructor */
    function PPWidget() {
        parent::WP_Widget(false, $name = 'PlacedPosts', $widget_options = array('name' => __('Locus Posts', 'locus'),'description' => __('Display posts from specific category','locus')));;
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $text = apply_filters( 'widget_text', $instance['text'], $instance );
        $link = apply_filters( 'widget_text', $instance['link'], $instance );
        $time_format = apply_filters( 'widget_text', $instance['time_format'], $instance );
        $sortby = empty( $instance['sortby'] ) ? 'comment_count' : $instance['sortby'];
        $c = $instance['content'] ? '1' : '0';
        $e = $instance['excerpt'] ? '1' : '0';
        $t = $instance['thumb'] ? '1' : '0';
        $d = $instance['date'] ? '1' : '0';
        $lk = $instance['link'] ? '1' : '0';
        $cn = $instance['cat_name'] ? '1' : '0';
        $tag = isset($instance['tag']) ? $instance['tag'] : false;
        $number = isset($instance['number']) ? $instance['number'] : false;
        $category = isset($instance['category']) ? $instance['category'] : false;

        $q = new WP_Query(array('ignore_sticky_posts' => 1,'post_type'=>'post', 'posts_per_page'=>$number, 'orderby'=>$sortby,'category_name' => $category, 'tag' => $tag));
        ?>

        <?php echo $before_widget; ?>

<!-- Locus plugin block start -->
<div class="pp-container">
<div class="pp-description"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
<div class="pp-box<?php echo '-' . $category; ?>">
<?php if ($category == null) : ?>
<?php else : ?>
<?php if ( $cn ) {  ?><div class="pp-category-<?php echo $category ;?>">
<?php $idObj = get_category_by_slug($category); $linkc = get_category_link( $idObj->cat_ID);
echo '<h2><a href="' . $linkc . '"';
echo 'title="' . esc_attr( sprintf( __( "View all posts in %s" ), $idObj->cat_name ) ) . '">' . $idObj->cat_name . '</a></h2></div><div class="pp-category-description">'; echo category_description($idObj->cat_ID); ?>
</div><?php } ?>
<?php endif; ?>

<?php while ($q->have_posts()) : $q->the_post(); ?>
<div class="pp-cont-<?php echo $category; ?><?php if ($category == null) : ?><?php $cate=get_the_category(); echo $cate[0]->slug; ?><?php endif; ?>">

<?php if ($category == null) : ?>
<?php if ( $cn ) {  ?><div class="pp-category"><?php the_category(' ');?></div><?php } ?><?php endif; ?>

<?php if ( $t ) {  ?><?php if(has_post_thumbnail( )): ?><div class="pp-thumb"><div class="pp-img-wrap"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a></div></div><?php endif; ?><?php } ?>

<?php if ( $d ) {  ?><div class="pp-date"><?php the_time($time_format); ?></div><?php } ?>
<div class="pp-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
<?php if ( $c ) {  ?><?php the_content('') ?><?php } ?>
<?php if ( $e ) {  ?><?php the_excerpt() ?><?php } ?>
<?php if ( $lk ) {  ?><div class="pp-read-more"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php echo $link; ?></a></div><?php } ?>
<div class="pp-divisor"></div>
</div>
<?php endwhile; ?>
</div></div>
<!-- Locus plugin block ends-->

		<?php echo $after_widget; ?>

    <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata(); }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	$instance = $old_instance;
      $instance['link'] = ($new_instance['link']);
      $instance['time_format'] = strip_tags($new_instance['time_format']);
      $instance['number'] = ($new_instance['number']);
      $instance['tag'] = ($new_instance['tag']);
      $instance['category'] = ($new_instance['category']);
      $instance['content'] = !empty($new_instance['content']) ? 1 : 0;
      $instance['excerpt'] = !empty($new_instance['excerpt']) ? 1 : 0;
      $instance['thumb'] = !empty($new_instance['thumb']) ? 1 : 0;
      $instance['date'] = !empty($new_instance['date']) ? 1 : 0;
      $instance['cat_name'] = !empty($new_instance['cat_name']) ? 1 : 0;

	 		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		    else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		  $instance['filter'] = isset($new_instance['filter']);

    	if ( in_array( $new_instance['sortby'], array( 'title', 'date', 'author', 'ID', 'rand', 'modified', 'comment_count' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
	    	} else {
			$instance['sortby'] = 'comment_count';
	    	}
     return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    $instance = wp_parse_args( (array) $instance, array(  'text' => '', 'link' => __('Read more...','locus'),'time_format' => 'm/d/Y','sortby' => 'comment_count','category' => false,'tag' => false, 'number'=> false ) );
    $text = esc_textarea($instance['text']);
    $link = ($instance['link']);
    $time_format = esc_textarea($instance['time_format']);
    $content = isset($instance['content']) ? (bool) $instance['content'] :false;
    $excerpt = isset($instance['excerpt']) ? (bool) $instance['excerpt'] :false;
    $cat_name = isset($instance['cat_name']) ? (bool) $instance['cat_name'] :false;
    $thumb = isset($instance['thumb']) ? (bool) $instance['thumb'] :false;
    $date = isset($instance['date']) ? (bool) $instance['date'] :false;
    $link_cats = get_categories(array('hide_empty' => 1));
    $post_tags = get_tags(array('hide_empty' => 1));
    $items = range('1','10');
    ?>

         <p>
          <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Arbitrary text or HTML'); ?></label>
	      	<textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        </p>

	      <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('How many items would you like to display?'); ?></label>
          	<select id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>">
        		<?php
          	foreach ( $items as $item ) {
        			echo '<option value="' . $item . '"'
        				. ( $item == $instance['number'] ? ' selected="selected"' : '' )
        				. '>' . $item . "</option>\n"; } ?>
        	  </select>
        </p>

    		<p>
    			<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
    			<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
    				<option value="title"<?php selected( $instance['sortby'], 'title' ); ?>><?php _e('Post title','locus'); ?></option>
    				<option value="date"<?php selected( $instance['sortby'], 'date' ); ?>><?php _e('Post date','locus'); ?></option>
    				<option value="author"<?php selected( $instance['sortby'], 'author' ); ?>><?php _e( 'Post author','locus'); ?></option>
    				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Post ID','locus'); ?></option>
    				<option value="rand"<?php selected( $instance['sortby'], 'rand' ); ?>><?php _e( 'Random' ); ?></option>
    				<option value="modified"<?php selected( $instance['sortby'], 'modified' ); ?>><?php _e( 'Modified date','locus'); ?></option>
    				<option value="comment_count"<?php selected( $instance['sortby'], 'comment_count' ); ?>><?php _e( 'Popularity','locus'); ?></option>
    			</select>
    		</p>

      	<p>
          <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category'); ?>:</label>
      		<select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
          <option value=""><?php _e('Any Category','locus'); ?></option>
       		<?php
       		foreach ( $link_cats as $link_cat ) {
       			echo '<option value="' . ($link_cat->slug) . '"'
       				. ( $link_cat->slug == $instance['category'] ? ' selected="selected"' : '' )
       				. '>' . $link_cat->name . "&nbsp;(". $link_cat->count . ")" ."</option>\n";  } ?>
      	 </select>
        </p>

      	<p>
          <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag','locus'); ?>:</label>
      		<select class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>">
      		<option value=""><?php _e('Any tag','locus'); ?></option>
      	 	<?php
      		foreach ( $post_tags as $post_tag ) {
      			echo '<option value="' . ($post_tag->slug) . '"'
      				. ( $post_tag->slug == $instance['tag'] ? ' selected="selected"' : '' )
      				. '>' . $post_tag->name . "&nbsp;(". $post_tag->count .")"."</option>\n";
      		}
      		?>
      		</select>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link text','locus'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('time_format'); ?>"><?php _e('Time format','locus'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('time_format'); ?>" type="text" value="<?php echo $time_format; ?>" />
        </p>

    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>"<?php checked( $content ); ?> />
		<label for="<?php echo $this->get_field_id('content'); ?>"><?php _e( 'Show content','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>"<?php checked( $excerpt ); ?> />
		<label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e( 'Show excerpt','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>"<?php checked( $thumb ); ?> />
		<label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e( 'Display thumbnail if available','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>"<?php checked( $date ); ?> />
		<label for="<?php echo $this->get_field_id('date'); ?>"><?php _e( 'Display post date','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('cat_name'); ?>" name="<?php echo $this->get_field_name('cat_name'); ?>"<?php checked( $cat_name ); ?> />
		<label for="<?php echo $this->get_field_id('cat_name'); ?>"><?php _e( 'Display category name and link','locus' ); ?></label><br />

        <?php
    }

} // class PPWidget


/**
 * PPostTypeWidget Class
 */
class PPostTypeWidget extends WP_Widget {

    /** constructor */
    function PPostTypeWidget() {
        parent::WP_Widget(false, $name = 'PlacedPostTypes', $widget_options = array('name' => __('Locus Posts Types', 'locus'),'description' => __('Display available posts types','arras')));;
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $text = apply_filters( 'widget_text', $instance['text'], $instance );
        $link = apply_filters( 'widget_text', $instance['link'], $instance );
        $time_format = apply_filters( 'widget_text', $instance['time_format'], $instance );
        $sortby = empty( $instance['sortby'] ) ? 'comment_count' : $instance['sortby'];
        $c = $instance['content'] ? '1' : '0';
        $e = $instance['excerpt'] ? '1' : '0';
        $t = $instance['thumb'] ? '1' : '0';
        $d = $instance['date'] ? '1' : '0';
        $lk = $instance['link'] ? '1' : '0';

        $number = isset($instance['number']) ? $instance['number'] : false;
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : false;

        $q = new WP_Query(array('ignore_sticky_posts' => 1,'post_type'=>$post_type, 'posts_per_page'=>$number, 'orderby'=>$sortby));
        ?>

        <?php echo $before_widget; ?>
<!-- Locus plugin block start-->
<div class="pp-container">
<div class="pp-description"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
<div class="pp-box<?php echo '-' . $category; ?>">
<?php while ($q->have_posts()) : $q->the_post(); ?>
<div class="pp-posttype">
<?php if ( $t ) {  ?><?php if(has_post_thumbnail( )): ?><div class="pp-thumb"><div class="pp-img-wrap"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a></div></div><?php endif; ?><?php } ?>
<?php if ( $d ) {  ?><div class="pp-date"><?php the_time($time_format); ?></div><?php } ?>
<div class="pp-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
<?php if ( $c ) {  ?><?php the_content('') ?><?php } ?>
<?php if ( $e ) {  ?><?php the_excerpt() ?><?php } ?>
<?php if ( $lk ) {  ?><div class="pp-read-more"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php echo $link; ?></a></div><?php } ?>
<div class="pp-divisor"></div>
</div>
<?php endwhile; ?>
</div></div>
<!-- Locus plugin block end -->
		<?php echo $after_widget; ?>

    <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata(); }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	$instance = $old_instance;
      $instance['link'] = ($new_instance['link']);
      $instance['time_format'] = strip_tags($new_instance['time_format']);
      $instance['number'] = ($new_instance['number']);
      $instance['tag'] = ($new_instance['tag']);
      $instance['post_type'] = ($new_instance['post_type']);
      $instance['content'] = !empty($new_instance['content']) ? 1 : 0;
      $instance['excerpt'] = !empty($new_instance['excerpt']) ? 1 : 0;
      $instance['thumb'] = !empty($new_instance['thumb']) ? 1 : 0;
      $instance['date'] = !empty($new_instance['date']) ? 1 : 0;


	 		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		    else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		  $instance['filter'] = isset($new_instance['filter']);

    	if ( in_array( $new_instance['sortby'], array( 'title', 'date', 'author', 'ID', 'rand', 'modified', 'comment_count' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
	    	} else {
			$instance['sortby'] = 'comment_count';
	    	}
     return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    $instance = wp_parse_args( (array) $instance, array(  'text' => '', 'link' => __('Read more...','locus'),'time_format' => 'm/d/Y','sortby' => 'comment_count','post_type' => false,'tag' => false, 'number'=> false ) );
    $text = esc_textarea($instance['text']);
    $link = ($instance['link']);
    $time_format = esc_textarea($instance['time_format']);
    $content = isset($instance['content']) ? (bool) $instance['content'] :false;
    $excerpt = isset($instance['excerpt']) ? (bool) $instance['excerpt'] :false;
    $cat_name = isset($instance['cat_name']) ? (bool) $instance['cat_name'] :false;
    $thumb = isset($instance['thumb']) ? (bool) $instance['thumb'] :false;
    $date = isset($instance['date']) ? (bool) $instance['date'] :false;

    $post_types = get_post_types('','names');

    $post_tags = get_tags(array('hide_empty' => 1));
    $items = range('1','10');
    ?>

         <p>
          <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Arbitrary text or HTML'); ?></label>
	      	<textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        </p>

	      <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('How many items would you like to display?'); ?></label>
          	<select id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>">
        		<?php
          	foreach ( $items as $item ) {
        			echo '<option value="' . $item . '"'
        				. ( $item == $instance['number'] ? ' selected="selected"' : '' )
        				. '>' . $item . "</option>\n"; } ?>
        	  </select>
        </p>

    		<p>
    			<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
    			<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
    				<option value="title"<?php selected( $instance['sortby'], 'title' ); ?>><?php _e('Post title','locus'); ?></option>
    				<option value="date"<?php selected( $instance['sortby'], 'date' ); ?>><?php _e('Post date','locus'); ?></option>
    				<option value="author"<?php selected( $instance['sortby'], 'author' ); ?>><?php _e( 'Post author','locus'); ?></option>
    				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Post ID','locus'); ?></option>
    				<option value="rand"<?php selected( $instance['sortby'], 'rand' ); ?>><?php _e( 'Random' ); ?></option>
    				<option value="modified"<?php selected( $instance['sortby'], 'modified' ); ?>><?php _e( 'Modified date','locus'); ?></option>
    				<option value="comment_count"<?php selected( $instance['sortby'], 'comment_count' ); ?>><?php _e( 'Popularity','locus'); ?></option>
    			</select>
    		</p>

      	<p>
          <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Available post types'); ?>:</label>
      		<select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
       		<?php  echo $post_types;
       		foreach ( $post_types as $post_type ) {
       			echo '<option value="' . ($post_type) . '"'
       				. ( $post_type == $instance['post_type'] ? ' selected="selected"' : '' )
       				. '>' . $post_type ."</option>\n";  } ?>
      	 </select>
        </p>



        <p>
          <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link text','locus'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('time_format'); ?>"><?php _e('Time format','locus'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('time_format'); ?>" type="text" value="<?php echo $time_format; ?>" />
        </p>

    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>"<?php checked( $content ); ?> />
		<label for="<?php echo $this->get_field_id('content'); ?>"><?php _e( 'Show content','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>"<?php checked( $excerpt ); ?> />
		<label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e( 'Show excerpt','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>"<?php checked( $thumb ); ?> />
		<label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e( 'Display thumbnail if available','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>"<?php checked( $date ); ?> />
		<label for="<?php echo $this->get_field_id('date'); ?>"><?php _e( 'Display post date','locus' ); ?></label><br />

        <?php
    }

} // class PPostTypeWidget



/**
 * PlacedSingleContent Class
 */
class PlacedSingleContent extends WP_Widget {

    /** constructor */
    function PlacedSingleContent() {
        parent::WP_Widget(false, $name = 'PlacedSingleContent', $widget_options = array('name' => __('Locus Single Post & Page', 'locus'),'description' => __('Display a single post or page','locus')));;
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );

        $link = apply_filters( 'widget_text', $instance['link'], $instance );
        $slug = apply_filters( 'widget_text', $instance['slug'], $instance );
        $time_format = apply_filters( 'widget_text', $instance['time_format'], $instance );

        $c = $instance['content'] ? '1' : '0';
        $e = $instance['excerpt'] ? '1' : '0';
        $t = $instance['thumb'] ? '1' : '0';
        $d = $instance['date'] ? '1' : '0';
        $lk = $instance['link'] ? '1' : '0';

        $number = isset($instance['number']) ? $instance['number'] : false;
        $type = isset($instance['type']) ? $instance['type'] : false;

        $q = new WP_Query(array($type=>$slug));
        ?>

        <?php echo $before_widget; ?>

<!-- Locus plugin block starts-->
<div class="pp-container">
<div class="pp-box<?php echo $slug; ?>">
<?php while ($q->have_posts()) : $q->the_post(); ?>
<div class="pp-anycontent">
<?php if ( $t ) {  ?><?php if(has_post_thumbnail( )): ?><div class="pp-thumb"><div class="pp-img-wrap"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a></div></div><?php endif; ?><?php } ?>
<?php if ( $d ) {  ?><div class="pp-date"><?php the_time($time_format); ?></div><?php } ?>
<div class="pp-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
<?php if ( $c ) {  ?><?php the_content('') ?><?php } ?>
<?php if ( $e ) {  ?><?php the_excerpt() ?><?php } ?>
<?php if ( $lk ) {  ?><div class="pp-read-more"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php echo $link; ?></a></div><?php } ?>
<div class="pp-divisor"></div>
</div>
<?php endwhile; ?>
</div></div>
<!-- Locus plugin block end -->
        <?php echo $after_widget; ?>

    <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_posts', $cache, 'widget');   } 

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	$instance = $old_instance;
      $instance['link'] = ($new_instance['link']);
      $instance['slug'] = strip_tags($new_instance['slug']);
      $instance['time_format'] = strip_tags($new_instance['time_format']);
      $instance['type'] = ($new_instance['type']);
      $instance['content'] = !empty($new_instance['content']) ? 1 : 0;
      $instance['excerpt'] = !empty($new_instance['excerpt']) ? 1 : 0;
      $instance['thumb'] = !empty($new_instance['thumb']) ? 1 : 0;
      $instance['date'] = !empty($new_instance['date']) ? 1 : 0;

     return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    $instance = wp_parse_args( (array) $instance, array(  'link' => __('Read more...','locus'),'time_format' => 'm/d/Y','type' => false,'slug'=> 'about' ) );
    $type = ($instance['type']);
    $link = ($instance['link']);
    $slug = esc_textarea($instance['slug']);
    $time_format = esc_textarea($instance['time_format']);
    $content = isset($instance['content']) ? (bool) $instance['content'] :false;
    $excerpt = isset($instance['excerpt']) ? (bool) $instance['excerpt'] :false;
    $thumb = isset($instance['thumb']) ? (bool) $instance['thumb'] :false;
    $date = isset($instance['date']) ? (bool) $instance['date'] :false;

    ?>

      	<p>
          <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Available post types'); ?>:</label>
      		<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
          <option value="pagename"<?php selected( $instance['type'], 'pagename' ); ?>>page</option>
          <option value="name"<?php selected( $instance['type'], 'name' ); ?>>post</option>
      	 </select>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('slug'); ?>"><?php _e('Slug'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('slug'); ?>" name="<?php echo $this->get_field_name('slug'); ?>" type="text" value="<?php echo $slug; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link text','locus'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('time_format'); ?>"><?php _e('Time format','locus'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('time_format'); ?>" type="text" value="<?php echo $time_format; ?>" />
        </p>

    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>"<?php checked( $content ); ?> />
		<label for="<?php echo $this->get_field_id('content'); ?>"><?php _e( 'Show content','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>"<?php checked( $excerpt ); ?> />
		<label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e( 'Show excerpt','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>"<?php checked( $thumb ); ?> />
		<label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e( 'Display thumbnail if available','locus' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>"<?php checked( $date ); ?> />
		<label for="<?php echo $this->get_field_id('date'); ?>"><?php _e( 'Display post date','locus' ); ?></label><br />

        <?php
    }

} // class PlacedSingleContent





?>
