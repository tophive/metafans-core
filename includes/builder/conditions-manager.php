<?php


class Tophive_Conditions_Manager{
    public function resolve_single_condition($rule) {
		
		if ($rule['rule'] === 'singulars') {
			return is_singular();
		}

		if ($rule['rule'] === 'archives') {
			return is_archive();
		}

		if ($rule['rule'] === '404') {
			return is_404();
		}

		if ($rule['rule'] === 'search') {
			return is_search();
		}

		if ($rule['rule'] === 'blog') {
			return ! is_front_page() && is_home();
		}

		if ($rule['rule'] === 'front_page') {
			return is_front_page();
		}

		if ($rule['rule'] === 'woo_shop') {
			return function_exists('is_shop') && is_shop();
		}

		if ($rule['rule'] === 'single_post') {
			return is_singular('post');
		}

		if ($rule['rule'] === 'all_post_archives') {
			return is_post_type_archive('post');
		}

		if ($rule['rule'] === 'post_categories') {
			return is_category();
		}

		if ($rule['rule'] === 'post_tags') {
			return is_tag();
		}

		if ($rule['rule'] === 'single_page') {
			return is_singular('page');
		}

		if ($rule['rule'] === 'woo_product_single') {
			return function_exists('is_product') && is_product();
		}

		if ($rule['rule'] === 'all_product_archives') {
			if (function_exists('is_shop')) {
				return is_shop() || is_product_tag() || is_product_category();
			}
		}

		if ($rule['rule'] === 'all_product_categories') {
			if (function_exists('is_shop')) {
				return is_product_category();
			}
		}

		if ($rule['rule'] === 'all_product_tags') {
			if (function_exists('is_shop')) {
				return is_product_tag();
			}
		}

        if ( $rule['rule'] === 'post_id' || $rule['rule'] === 'page_id' || $rule['rule'] === 'custom_post_type_id' ){
            if( is_single() ){
                if( $rule['payload'] == get_the_ID() ){
                    return true;
                }
            }
        }
        if ($rule['rule'] === 'all') {
			return true;
		}

		return false;
	}
    public function condition_matches($rules = [], $args = []) {
		$args = wp_parse_args($args, [
			'relation' => 'OR',
			// prefix | current-screen
			'strategy' => 'current-screen'
		]);

		if (empty($rules)) {
			return false;
		}

		// Check if it looks like a normal rules array. If it doesn't -- bail out.
		if (! isset($rules[0]) || ! isset($rules[0]['conditions'])) {
			return false;
		}

		$all_includes = array_filter($rules, function ($el) {
			return $el['conditions']['type'] === 'in';
		});

		$all_excludes = array_filter($rules, function ($el) {
			return $el['conditions']['type'] === 'out';
		});

        // return [$all_excludes, $all_includes];
        
		$resolved_includes = array_filter($all_includes, function ($el) use ($args) {
            return $this->resolve_single_condition($el['conditions']);
		});
        
		$resolved_excludes = array_filter($all_excludes, function ($el) use ($args) {
            return $this->resolve_single_condition($el['conditions']);
		});
         

		// If at least one exclusion is true -- return false
		if (! empty($resolved_excludes)) {
			return false;
		}

		if (empty($all_includes)) {
			return true;
		}

		if (! empty($all_includes)) {
			// If at least one inclusion is true - return true
			if ($args['relation'] === 'OR' && ! empty($resolved_includes)) {
				return $resolved_includes;
			}

			// For AND relation all includes need to be resolved
			if (
				$args['relation'] === 'AND'
				&&
				count($resolved_includes) === count($all_includes)
			) {
				return $resolved_includes;
			}
		}

		return false;
	}
}