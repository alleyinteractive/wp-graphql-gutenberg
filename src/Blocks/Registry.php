<?php

namespace WPGraphQLGutenberg\Blocks;

if ( ! defined( 'WP_GRAPHQL_GUTENBERG_REGISTRY_OPTION_NAME' ) ) {
	define( 'WP_GRAPHQL_GUTENBERG_REGISTRY_OPTION_NAME', 'wp_graphql_gutenberg_block_types' );
}

use GraphQL\Error\ClientAware;

class Registry {
	public static function normalize($block_types) {
		return array_reduce(
			$block_types,
			function ($arr, $block_type) {
				$arr[$block_type['name']] = $block_type;
				return $arr;
			},
			[]
		);
	}

	public static function update_registry( $registry ) {
		// Merge the new registry with the old registry. This will ensure that we don't remove any blocks that were
		// available in another context (e.g. another post type) but not this one.
		$old_values = get_option( WP_GRAPHQL_GUTENBERG_REGISTRY_OPTION_NAME, [] ) ?? [];
		return update_option( WP_GRAPHQL_GUTENBERG_REGISTRY_OPTION_NAME, array_merge( $old_values, $registry ), false );
	}

	/**
	 * @throws RegistryNotSourcedException
	 */
	public static function get_registry() {
		$registry = get_option( WP_GRAPHQL_GUTENBERG_REGISTRY_OPTION_NAME ) ?? null;

		if ( empty( $registry ) ) {
			throw new RegistryNotSourcedException(
				__(
					'Client side block registry is missing. You need to open up gutenberg or load it from WPGraphQLGutenberg Admin page.',
					'wp-graphql-gutenberg'
				)
			);
		}

		return $registry;
	}

	public static function delete_registry() {
		return delete_option( WP_GRAPHQL_GUTENBERG_REGISTRY_OPTION_NAME );
	}
}
