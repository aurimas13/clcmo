<?php
/**
 * Hook to remove VC version of font awesome from front-end and back-end
 * Made for Visual Composer in version 4.12
 */
function a13fe_vc_remove_vc_font_awesome(){
	wp_deregister_style( 'font-awesome' );
}
add_action( 'wp_footer', 'a13fe_vc_remove_vc_font_awesome', 1 );


/**
 * Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
 */
add_action('vc_before_init', 'a13fe_vc_set_as_bundled');
function a13fe_vc_set_as_bundled(){
	if(defined('A13FRAMEWORK_TPL_SLUG')){
		$list = array(
			'apollo13-framework-extensions',
			'starter',
			'a13agency',
			'photoproof'
		);
		if(in_array(A13FRAMEWORK_TPL_SLUG, $list)){
			vc_set_as_theme();
		}
	}
}



/**
 * Function to hook JavaScript modification for vc_grid
 */
function a13fe_vc_modify_post_grid_filter_hook(){
	wp_add_inline_script( 'vc_grid', a13fe_vc_modify_post_grid_filter() );
}

/*
 * wp_footer action cause that is the best time, when WordPress already know about vc_grid script
 * and it is still before printing this script
 */
add_action( 'wp_footer', 'a13fe_vc_modify_post_grid_filter_hook', 1 );

/**
 * Returns JavaScript that changes behaviour of filter in vc_grid
 * It works for Post grid & Post Masonry grid elements
 * Made for Visual Composer in version 4.12
 *
 * @return string inline JavaScript
 */
function a13fe_vc_modify_post_grid_filter(){
	$inline_script = '
!function ($) {
    //normal post grid element
	//hiding only elements that do not belong to current category
    vcGridStyleAll.prototype.showItems = function () {
		var all_els = this.$content.find(".vc_grid-item"),
            $els    = this.filterValue.length ? all_els.filter(this.filterValue) : all_els;

		all_els.not($els).removeClass("vc_visible-item");
		this.setIsLoading();
		var animation = this.$content.parents(".vc_grid-container").data("initial-loading-animation");
		vcGridSettings.addItemsAnimation = animation, $els.addClass("vc_visible-item " + ("none" !== vcGridSettings.addItemsAnimation ? vcGridSettings.addItemsAnimation + " animated" : "")), this.unsetIsLoading(), $(window).trigger("grid:items:added", this.$el)
	};

	//removed hiding all items
    vcGridStyleAll.prototype.filter = function (filter) {
		if (filter = _.isUndefined(filter) || "*" === filter ? "" : filter, this.filterValue == filter)return !1;
		var animation = this.$content.parents(".vc_grid-container").data("initial-loading-animation");
		vcGridSettings.addItemsAnimation = animation, this.filterValue = filter, _.defer(this.showItems)
	};



    //masonry post grid element
	//hiding only elements that do not belong to current category
    vcGridStyleAllMasonry.prototype.showItems = function () {
		var all_els = this.$content.find(".vc_grid-item"),
			$els    = this.filterValue.length ? all_els.filter(this.filterValue) : all_els,
			self    = this;

		all_els.not($els).removeClass("vc_visible-item");
		this.setIsLoading(), $els.imagesLoaded(function () {

			$els.addClass("vc_visible-item"), self.setItems($els), self.filtered && (self.filtered = !1, self.setMasonry()), self.unsetIsLoading(), window.vc_prettyPhoto(), $(window).trigger("grid:items:added", self.$el)
		})
	};

	//removed hiding all items
    vcGridStyleAllMasonry.prototype.filter = function (filter) {
		return filter = _.isUndefined(filter) || "*" === filter ? "" : filter, this.filterValue == filter ? !1 : (this.filterValue = filter, this.$content.data("masonry") && this.$content.masonry("destroy"), this.masonryEnabled = !1, this.$content.find(".vc_grid-item" + this.filterValue), this.filtered = !0, $(window).resize(this.setMasonry), this.setMasonry(), void this.showItems())
    };

}(window.jQuery);

';

	return $inline_script;
}