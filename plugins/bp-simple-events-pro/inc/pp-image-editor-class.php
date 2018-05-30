<?php
/**
 * uses WP Image Editor
 * resize to large dims per media settings, without cropping, so huge imgs do not sit in uploads dir
 * rotate if necessary - jpg only
 */

if ( !class_exists( 'PP_ImageEditor' ) ) {

	class PP_ImageEditor {

		public function __construct() {
			add_filter( 'wp_handle_upload', array( $this, 'filter_wp_handle_upload' ), 1, 3 );
		}

		public function filter_wp_handle_upload( $file ) {
			$this->fixImage( $file['file'], $file['type'] );
			return $file;
		}

		public function fixImage( $file, $type ) {

			if ( is_callable('exif_read_data') ) {

				$image = wp_get_image_editor( $file );

				if ( ! is_wp_error( $image ) ) {

					$image->resize( get_option('large_size_w'), get_option('large_size_h'), false );

					$exif = @exif_read_data( $file );

					if( !empty( $exif['Orientation'] ) ) {

						switch( $exif['Orientation'] ) {
							case 3:
								$image->rotate( 180 );
							break;
							case 6:
								$image->rotate( -90 );
							break;
							case 8:
								$image->rotate( 90 );
							break;
						}
					}

					$image->save( $file );
				}
			}
		}
	}

	new PP_ImageEditor();
}
?>