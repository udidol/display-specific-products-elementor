<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Notice if the Elementor is not active
if ( ! did_action( 'elementor/loaded' ) ) {
	// add_action( 'admin_notices', 'udi_dsc_fail_load' );
	return;
}


class Udi_DSPE_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'udi_dsc';
	}

	public function get_title() {
		return 'Display Specific Products';
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {

        global $dspeProductsArray;

        $this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'udi_dsc' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
            
            $this->add_control(
                'products_list',
                [
                    'label' => __( 'Choose Products', 'udi_dsc' ),
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => $dspeProductsArray,
                    'section' => 'content_section',
                    'default' => [ '' ],
                ]
            );

            $this->add_control(
                'products_per_row',
                [
                    'label' => __( 'Products Per Row', 'udi_dsc' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '1',
                    'options' => [
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                    ],
                ]
            );

            $this->add_control(
                'randomize',
                [
                    'label' => __( 'Randomize Product Order', 'udi_dsc' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'udi_dsc' ),
                    'label_off' => __( 'Off', 'udi_dsc' ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );

        $this->end_controls_section();

	}

	protected function render( $instance = [] ) {
        
        global $dspeProductsArray;

		$settings = $this->get_settings_for_display();
    
        // Get number of products per row from user's chosen widget settings, and make it into a global var.
        $GLOBALS['products_per_row'] = !empty($settings['products_per_row']) ? intval($settings['products_per_row']) : 1;
        $products_per_row = $GLOBALS['products_per_row'];

        if ( 'yes' === $settings['randomize'] ) {
			shuffle($settings['products_list']);
		}

        ?>

        <style>
        <?php
        if ( !empty($settings['products_list']) && ( count($settings['products_list']) % $products_per_row ) === 0 ) : ?>
            .elementor-widget-udi_dsc .elementor-widget-container {
                justify-content: space-between;
		    }
        <?php endif;

        if ( !empty($settings['products_list']) && ( count($settings['products_list']) % $products_per_row ) !== 0 ) : ?>
            
            .udi-dsc-container {
                margin-right: 1%;
            }

            .elementor-widget-udi_dsc .elementor-widget-container {
                justify-content: flex-start;
		    }
            
            <?php if ( $products_per_row == 2 ) : ?>
                .udi-dsc-container {
                    margin-right: 2%;
                }
            <?php endif; ?>

            <?php if ( $products_per_row == 3 ) : ?>
                .udi-dsc-container {
                    margin-right: 1.5%;
                }
            <?php endif; ?>

            <?php if ( $products_per_row == 4 ) : ?>
                .udi-dsc-container {
                    margin-right: 1.33%;
                }
            <?php endif; ?>

            .udi-dsc-container:nth-of-type(<?php echo $products_per_row; ?>n+<?php echo $products_per_row; ?>) {
                margin-right: 0;
            }
        <?php endif; ?>

        .udi-dsc-container {
            width: <?php echo ((100 / $products_per_row)-1); ?>%;
            margin-bottom: 1em;
        }
        
        <?php if ($products_per_row > 1): ?>
            @media only screen and (max-width: 800px) {
                .udi-dsc-container {
                    width: 49%;
                }

                .udi-dsc-container:nth-of-type(<?php echo $products_per_row; ?>n+<?php echo $products_per_row; ?>) {
                    margin-right: 2%;
                }

                .udi-dsc-container:nth-of-type(2n+2) {
                    margin-right: 0;
                }
            }

            @media only screen and (max-width: 480px) {
                .udi-dsc-container {
                    width: 100%;
                }

                .udi-dsc-container:nth-of-type(<?php echo $products_per_row; ?>n+<?php echo $products_per_row; ?>) {
                    margin-right: 0;
                }

                .udi-dsc-container:nth-of-type(2n+2) {
                    margin-right: 0;
                }
            }
        <?php endif; ?>
        
        /* zero left padding on first row element */
        /* .udi-dsc-container:nth-of-type(<?php echo $products_per_row; ?>n+1) {
			padding-left: 0.5%;
            padding-right: 0.5%;
		} */
        /* half the left padding on second(!) row element */
        /* .udi-dsc-container:nth-of-type(<?php echo $products_per_row; ?>n+2) {
			padding-left: 0.5%;
            padding-right: 0.5%;
		} */
        /* half the right padding on second to last(!) row element */
        /* .udi-dsc-container:nth-of-type(<?php echo $products_per_row; ?>n+<?php echo $products_per_row-1; ?>) {
            padding-right: 0.5%;
            padding-left: 0.5%;
		} */
        /* zero right padding on last row element */
        /* .udi-dsc-container:nth-of-type(<?php echo $products_per_row; ?>n+<?php echo $products_per_row; ?>) {
			padding-right: 0.5%;
            padding-left: 0.5%;
		} */
        </style>

        <?php

        if ( !empty($settings['products_list'][0]) ) {
            foreach ($settings['products_list'] as $productID) {
                $product_image = (wp_get_attachment_image_src( get_post_thumbnail_id( intval($productID) ), 'large' ) !== false ) ? wp_get_attachment_image_src( get_post_thumbnail_id( intval($productID) ), 'large' ) : array( esc_url(plugins_url().'/display-specific-products-elementor/img/placeholder.png') );
                $product_image_url = $product_image[0];
                $defaultProductTitle = !empty($productID) ? esc_attr( get_the_title($productID) ) : 'Unavailable: No product was chosen';

                ?>

                <div class="udi-dsc-container">
                    <div class="udi-dsc-product">
                        <div class="udi-dsc-product-image">
                            <a href="<?php get_permalink( $productID ) ?>"><img src="<?php echo $product_image_url; ?>"></a>
                        </div>
                        <div class="udi-dsc-product-title">
                            <a href="<?php get_permalink( $productID ) ?>"><?php echo $defaultProductTitle; ?></a>
                        </div>
                    </div><!-- /udi-dsc-product -->
                </div><!-- /udi-dsc-container -->

                <?php
            }
        }
        else { // If no products are selected
            ?>
            
            <div class="udi-dsc-container">
                <div class="udi-dsc-product">
                    <div class="udi-dsc-product-image">
                        <img src="<?php echo esc_url(plugins_url().'/display-specific-products-elementor/img/placeholder.png'); ?>">
                    </div>
                    <div class="udi-dsc-product-title">
                        <p>Unavailable: No product was chosen</p>
                    </div>
                </div><!-- /udi-dsc-product -->
            </div><!-- /udi-dsc-container -->
            
            <?php
        }
        

	}
  
    protected function content_template() {}
  
    public function render_plain_content( $instance = [] ) {}

}

/**
 * Register Widget
 *
 * @since 1.0.0
 *
 * @access private
 */
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Udi_DSPE_Widget() );
//Plugin::instance()->widgets_manager->register_widget( 'Elementor\Udi_DSPE_Widget' );
