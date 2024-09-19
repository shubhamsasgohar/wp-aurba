<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } elseif ( defined( 'WP_CLI' ) && WP_CLI ) { return; }


if( ! class_exists('Geot_Hummingbird') ){
	class Geot_Hummingbird{
		public $default_country = 'en';
		public $geodb_path = '';//Please login admin and then access this url to retrieve the geolocation db path: your-site.com/wp-admin?wpmudev-retrieve-geodb-path

		public $user_ip_test = '';//Enter an IP address to test.

		public function __construct(){
			add_filter( 'wphb_shold_cache_request_pre', array( $this, 'should_cache' ), 999 );

			add_action( 'init', array( $this, 'maybe_change_advanced_cache_file' ), 999 );

		}

		public function maybe_change_advanced_cache_file(){
			$old_md5 = get_site_option( 'wpmudev-hb-advanced-cache' );
			$advanced_cache_file = WP_CONTENT_DIR .'/advanced-cache.php';
			if( @file_exists( $advanced_cache_file ) ){
				$md5 = md5_file( $advanced_cache_file );
				if( $old_md5 !== $md5 ){
					$content = @file_get_contents( $advanced_cache_file );

					if( ! strpos( $content, 'wpmudev-hmb-cache-base-on-user-country.php' ) ){
						$content = str_replace("define( 'WPHB_ADVANCED_CACHE', true );", base64_decode('ZGVmaW5lKCAnV1BIQl9BRFZBTkNFRF9DQUNIRScsIHRydWUgKTsKCSR3cG11ZGV2X2hiX2N1c3RvbV9tdV9maWxlID0gV1BfQ09OVEVOVF9ESVIgLicvbXUtcGx1Z2lucy93cG11ZGV2LWhtYi1jYWNoZS1iYXNlLW9uLXVzZXItY291bnRyeS5waHAnOwoJaWYoIEBmaWxlX2V4aXN0cyggJHdwbXVkZXZfaGJfY3VzdG9tX211X2ZpbGUgKSApewoJCXJlcXVpcmVfb25jZSggJHdwbXVkZXZfaGJfY3VzdG9tX211X2ZpbGUgKTsKCX0'), $content );

						if( file_put_contents( $advanced_cache_file, $content ) ){
							$md5 = md5_file( $advanced_cache_file );
						}
					}
					update_site_option( 'wpmudev-hb-advanced-cache', $md5 );
				}
			}
		}

		public function should_cache( $should_cache ){
			if( $should_cache ){
				$this->maybe_custom_cache_path();
			}
			return $should_cache;
		}

		public function maybe_custom_cache_path(){
			$user_country = geot_country_code();
			if( $user_country ){
				$_COOKIE['wp-postpass_current_country'] = strtolower($user_country);
			}
		}

	}

	new Geot_Hummingbird();
}
