<?php
namespace ModernAIChatElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor AI Chat Widget.
 *
 * Elementor widget that displays an AI powered chat interface.
 *
 * @since 1.0.0
 */
class ModernChatWidget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve AI Chat widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'modern-ai-chat';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve AI Chat widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'AI Chat Widget', 'modern-ai-chat-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve AI Chat widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-comments'; // Or 'eicon-chat'
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url() {
		return 'https://example.com/docs/ai-chat-widget'; // Replace with your actual help URL
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the AI Chat widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'ai-widgets' ]; // Custom category defined in the main plugin file
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the AI Chat widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'ai', 'chat', 'bot', 'assistant', 'support', 'modern' ];
	}

    /**
	 * Register AI Chat widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content_chat_settings',
			[
				'label' => esc_html__( 'Chat Settings', 'modern-ai-chat-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'webhook_url',
			[
				'label' => esc_html__( 'Webhook URL', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'url',
				'placeholder' => esc_html__( 'https://your-webhook-url.com/api', 'modern-ai-chat-elementor' ),
				'label_block' => true,
				'description' => esc_html__( 'The URL your messages will be sent to.', 'modern-ai-chat-elementor' ),
                'default' => 'https://n8n-xtgtmcqc.eu-central-1.clawcloudrun.com/webhook/805766d2-1608-419d-8fc7-a161fe41ac05', // Default to dev webhook
			]
		);

		$this->add_control(
			'chat_header_title',
			[
				'label' => esc_html__( 'Chat Header Title', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Chat with us', 'modern-ai-chat-elementor' ),
				'placeholder' => esc_html__( 'Chat with us', 'modern-ai-chat-elementor' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'bot_name',
			[
				'label' => esc_html__( 'Bot Name', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'AI Assistant', 'modern-ai-chat-elementor' ),
				'placeholder' => esc_html__( 'AI Assistant', 'modern-ai-chat-elementor' ),
			]
		);

		$this->add_control(
			'bot_avatar',
			[
				'label' => esc_html__( 'Bot Avatar', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(), // Default placeholder
				],
				'description' => esc_html__( 'Upload an avatar for the bot.', 'modern-ai-chat-elementor' ),
			]
		);

		$this->add_control(
			'initial_greeting',
			[
				'label' => esc_html__( 'Initial Greeting Message', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Hello! How can I help you today?', 'modern-ai-chat-elementor' ),
				'placeholder' => esc_html__( 'Enter the first message the bot sends.', 'modern-ai-chat-elementor' ),
				'rows' => 3,
                'description' => esc_html__( 'This message will appear when the chat is first loaded.', 'modern-ai-chat-elementor' ),
			]
		);

		$this->add_control(
			'input_placeholder',
			[
				'label' => esc_html__( 'Input Field Placeholder', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Type your message...', 'modern-ai-chat-elementor' ),
				'placeholder' => esc_html__( 'Type your message...', 'modern-ai-chat-elementor' ),
			]
		);

		$this->end_controls_section();

        // Style Tab
		$this->start_controls_section(
			'section_style_color_scheme',
			[
				'label' => esc_html__( 'Color Scheme', 'modern-ai-chat-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
            'color_scheme',
            [
                'label' => esc_html__( 'Select Scheme', 'modern-ai-chat-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'light',
                'options' => [
                    'light'  => esc_html__( 'Light (Default)', 'modern-ai-chat-elementor' ),
                    'dark' => esc_html__( 'Dark', 'modern-ai-chat-elementor' ),
                    'gradient' => esc_html__( 'Gradient', 'modern-ai-chat-elementor' ),
                    'custom' => esc_html__( 'Custom', 'modern-ai-chat-elementor' ),
                ],
                'prefix_class' => 'modern-ai-chat-scheme--', // Used to apply scheme via CSS
				// 'selectors_dictionary' => [ // Temporarily removed for debugging
				// 	'light' => $this->get_color_scheme_settings('light'),
				// 	'dark' => $this->get_color_scheme_settings('dark'),
				// 	'gradient' => $this->get_color_scheme_settings('gradient'),
				// ],
				'frontend_available' => true, // Make available in JS
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_chat_window',
			[
				'label' => esc_html__( 'Chat Window', 'modern-ai-chat-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'chat_window_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat' => 'background-color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);

        $this->add_responsive_control(
            'chat_window_width',
            [
                'label' => esc_html__( 'Width', 'modern-ai-chat-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range' => [
                    'px' => [ 'min' => 200, 'max' => 1000, 'step' => 1 ],
                    '%' => [ 'min' => 10, 'max' => 100 ],
                    'vw' => [ 'min' => 10, 'max' => 100 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 360 ],
                'selectors' => [
                    '{{WRAPPER}} .modern-ai-chat' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'chat_window_height',
            [
                'label' => esc_html__( 'Height', 'modern-ai-chat-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vh' ],
                'range' => [
                    'px' => [ 'min' => 200, 'max' => 1000, 'step' => 1 ],
                    '%' => [ 'min' => 10, 'max' => 100 ],
                    'vh' => [ 'min' => 10, 'max' => 100 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 500 ],
                'selectors' => [
                    '{{WRAPPER}} .modern-ai-chat' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'chat_window_border',
				'selector' => '{{WRAPPER}} .modern-ai-chat',
                'condition' => ['color_scheme' => 'custom'],
			]
		);

		$this->add_responsive_control(
			'chat_window_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'chat_window_box_shadow',
				'selector' => '{{WRAPPER}} .modern-ai-chat',
			]
		);

		$this->end_controls_section();


        $this->start_controls_section(
			'section_style_header',
			[
				'label' => esc_html__( 'Header', 'modern-ai-chat-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'header_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__header' => 'background-color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);

        $this->add_control(
			'header_text_color',
			[
				'label' => esc_html__( 'Text Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__header-title' => 'color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_title_typography',
				'label' => esc_html__( 'Title Typography', 'modern-ai-chat-elementor' ),
				'scheme' => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modern-ai-chat__header-title',
			]
		);

        $this->add_responsive_control(
			'header_padding',
			[
				'label' => esc_html__( 'Padding', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_section();


        $this->start_controls_section(
			'section_style_user_message',
			[
				'label' => esc_html__( 'User Message Bubbles', 'modern-ai-chat-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control(
			'user_message_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__message--user .modern-ai-chat__message-bubble' => 'background-color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_control(
			'user_message_text_color',
			[
				'label' => esc_html__( 'Text Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__message--user .modern-ai-chat__message-text' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .modern-ai-chat__message--user .modern-ai-chat__message-time' => 'color: {{VALUE}}; opacity: 0.7;',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'user_message_typography',
				'selector' => '{{WRAPPER}} .modern-ai-chat__message--user .modern-ai-chat__message-text',
			]
		);
        $this->add_responsive_control(
			'user_message_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__message--user .modern-ai-chat__message-bubble' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_bot_message',
			[
				'label' => esc_html__( 'Bot Message Bubbles', 'modern-ai-chat-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control(
			'bot_message_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__message--bot .modern-ai-chat__message-bubble' => 'background-color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_control(
			'bot_message_text_color',
			[
				'label' => esc_html__( 'Text Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__message--bot .modern-ai-chat__message-text' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .modern-ai-chat__message--bot .modern-ai-chat__message-time' => 'color: {{VALUE}}; opacity: 0.7;',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bot_message_typography',
				'selector' => '{{WRAPPER}} .modern-ai-chat__message--bot .modern-ai-chat__message-text',
			]
		);
        $this->add_responsive_control(
			'bot_message_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__message--bot .modern-ai-chat__message-bubble' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_section();


        $this->start_controls_section(
			'section_style_input_area',
			[
				'label' => esc_html__( 'Input Area', 'modern-ai-chat-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control(
			'input_area_bg_color',
			[
				'label' => esc_html__( 'Area Background Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__input-area' => 'background-color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_control(
			'input_field_bg_color',
			[
				'label' => esc_html__( 'Input Field Background', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__input' => 'background-color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_control(
			'input_field_text_color',
			[
				'label' => esc_html__( 'Input Text Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__input' => 'color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_field_border',
				'selector' => '{{WRAPPER}} .modern-ai-chat__input',
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_control(
			'send_button_bg_color',
			[
				'label' => esc_html__( 'Send Button Background', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__send-button' => 'background-color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->add_control(
			'send_button_icon_color',
			[
				'label' => esc_html__( 'Send Button Icon Color', 'modern-ai-chat-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modern-ai-chat__send-button svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .modern-ai-chat__send-button i' => 'color: {{VALUE}};',
				],
                'condition' => ['color_scheme' => 'custom'],
			]
		);
        $this->end_controls_section();

	}

    protected function get_color_scheme_settings( $scheme_name ) {
		// These are placeholder values. They will be used by the CSS and potentially JS
		// to apply the schemes. The actual CSS for these schemes will be defined in chat-widget.css
		// This function is primarily for editor preview if we were to use it with `add_control_group` or complex selectors.
		// For simple class-based scheme switching, this is less critical for `selectors_dictionary`.
		// However, we can define what the 'custom' settings would revert to if a scheme is chosen.
		$schemes = [
			'light' => [
				'chat_window_bg_color' => '#F0F4F8',
				'header_bg_color' => '#FFFFFF',
                'header_text_color' => '#2C3E50',
				'user_message_bg_color' => '#DCF8C6',
                'user_message_text_color' => '#333333',
				'bot_message_bg_color' => '#FFFFFF',
                'bot_message_text_color' => '#333333',
                'input_area_bg_color' => '#FFFFFF',
                'input_field_bg_color' => '#F0F0F0',
                'input_field_text_color' => '#333333',
                'send_button_bg_color' => '#007AFF',
                'send_button_icon_color' => '#FFFFFF',
			],
			'dark' => [
				'chat_window_bg_color' => '#1E2732',
                'header_bg_color' => '#2C3A47',
                'header_text_color' => '#EAEAEA',
                'user_message_bg_color' => '#005C4B',
                'user_message_text_color' => '#EAEAEA',
                'bot_message_bg_color' => '#2C3A47',
                'bot_message_text_color' => '#EAEAEA',
                'input_area_bg_color' => '#2C3A47',
                'input_field_bg_color' => '#1E2732',
                'input_field_text_color' => '#EAEAEA',
                'send_button_bg_color' => '#007AFF',
                'send_button_icon_color' => '#FFFFFF',
			],
            'gradient' => [ // Example, actual gradient handled by CSS
				'chat_window_bg_color' => 'linear-gradient(135deg, #6B73FF 0%, #000DFF 100%)', // This won't directly apply via JS, CSS class will handle
                'header_bg_color' => 'rgba(255,255,255,0.1)',
                'header_text_color' => '#FFFFFF',
                'user_message_bg_color' => 'rgba(0, 92, 75, 0.8)',
                'user_message_text_color' => '#FFFFFF',
                'bot_message_bg_color' => 'rgba(44, 58, 71, 0.8)',
                'bot_message_text_color' => '#FFFFFF',
                'input_area_bg_color' => 'rgba(44, 58, 71, 0.5)',
                'input_field_bg_color' => 'rgba(30, 39, 50, 0.5)',
                'input_field_text_color' => '#FFFFFF',
                'send_button_bg_color' => '#007AFF',
                'send_button_icon_color' => '#FFFFFF',
            ]
		];
		return $schemes[$scheme_name] ?? $schemes['light'];
	}


	/**
	 * Render AI Chat widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
        $widget_id = 'modern-ai-chat-' . $this->get_id();

        $this->add_render_attribute( 'wrapper', 'class', 'modern-ai-chat' );
        // The color scheme class is handled by 'prefix_class' in the control itself.
        // $this->add_render_attribute( 'wrapper', 'class', 'modern-ai-chat-scheme--' . esc_attr( $settings['color_scheme'] ) );
		$this->add_render_attribute( 'wrapper', 'id', esc_attr( $widget_id ) );
        $this->add_render_attribute( 'wrapper', 'data-widget-id', esc_attr( $this->get_id() ) );

        // Pass settings to JavaScript
        $js_settings = [
            'widgetId' => $widget_id,
            'webhook_url' => esc_url( $settings['webhook_url'] ),
            'bot_name' => esc_html( $settings['bot_name'] ),
            'bot_avatar_url' => !empty( $settings['bot_avatar']['url'] ) ? esc_url( $settings['bot_avatar']['url'] ) : \Elementor\Utils::get_placeholder_image_src(),
            'initial_greeting' => wp_kses_post( $settings['initial_greeting'] ), // Using wp_kses_post for potential multi-line or simple HTML
            'input_placeholder' => esc_attr( $settings['input_placeholder'] ),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'modern_ai_chat_nonce' ) // This should ideally come from wp_localize_script for global ajax, but can be widget specific too.
        ];
        $this->add_render_attribute( 'wrapper', 'data-settings', wp_json_encode( $js_settings ) );

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="modern-ai-chat__header">
                <?php if ( ! empty( $settings['bot_avatar']['url'] ) ) : ?>
                    <img src="<?php echo esc_url( $settings['bot_avatar']['url'] ); ?>" alt="<?php echo esc_attr( $settings['bot_name'] ); ?>" class="modern-ai-chat__header-avatar">
                <?php else: ?>
                    <img src="<?php echo \Elementor\Utils::get_placeholder_image_src(); ?>" alt="<?php echo esc_attr( $settings['bot_name'] ); ?>" class="modern-ai-chat__header-avatar modern-ai-chat__header-avatar--placeholder">
                <?php endif; ?>
				<div class="modern-ai-chat__header-title"><?php echo esc_html( $settings['chat_header_title'] ); ?></div>
				<!-- Add other header elements if needed, e.g., close button -->
			</div>
			<div class="modern-ai-chat__messages-container">
                <div class="modern-ai-chat__messages">
                    <!-- Messages will be appended here by JavaScript -->
                    <!-- Typing indicator will be managed here by JavaScript -->
                </div>
            </div>
			<div class="modern-ai-chat__input-area">
				<input type="text" class="modern-ai-chat__input" placeholder="<?php echo esc_attr( $settings['input_placeholder'] ); ?>">
				<button class="modern-ai-chat__send-button" aria-label="<?php esc_attr_e('Send Message', 'modern-ai-chat-elementor'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/></svg>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Render AI Chat widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
        ?>
        <#
        var widgetId = 'modern-ai-chat-' + view.cid;

        // Default values for robustness in editor
        var botName = settings.bot_name || 'AI Assistant';
        var chatHeaderTitle = settings.chat_header_title || 'Chat with us';
        var initialGreeting = settings.initial_greeting || '';
        var inputPlaceholder = settings.input_placeholder || 'Type your message...';
        var webhookUrlSetting = settings.webhook_url || '';

        // Robustly get bot_avatar_url
        var botAvatarUrl = '<?php echo \Elementor\Utils::get_placeholder_image_src(); ?>';
        if ( settings.bot_avatar && typeof settings.bot_avatar === 'object' && settings.bot_avatar.url ) {
            botAvatarUrl = settings.bot_avatar.url;
        }

        var jsSettings = {
            widgetId: widgetId,
            webhook_url: webhookUrlSetting,
            bot_name: botName,
            bot_avatar_url: botAvatarUrl, // Already determined safely
            initial_greeting: initialGreeting,
            input_placeholder: inputPlaceholder,
            ajax_url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            nonce: '<?php echo wp_create_nonce( 'modern_ai_chat_nonce' ); ?>'
        };

        view.addRenderAttribute( 'wrapper', 'class', 'modern-ai-chat' );
        view.addRenderAttribute( 'wrapper', 'id', widgetId );
        view.addRenderAttribute( 'wrapper', 'data-widget-id', view.cid );
        // Pass only essential, safe data for the simplified preview
        var minimalJsSettings = {
             widgetId: widgetId,
             // webhook_url: webhookUrlSetting, // Not needed for minimal preview
             bot_name: botName,
             // bot_avatar_url: botAvatarUrl, // Not needed for minimal preview
             initial_greeting: initialGreeting, // Keep for basic display
             input_placeholder: inputPlaceholder,
             // ajax_url: '<?php echo admin_url( 'admin-ajax.php' ); ?>', // Not needed for minimal preview
             // nonce: '<?php echo wp_create_nonce( 'modern_ai_chat_nonce' ); ?>' // Not needed for minimal preview
        };
        view.addRenderAttribute( 'wrapper', 'data-settings', JSON.stringify(minimalJsSettings) );

        #>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
            <div class="modern-ai-chat__header">
                 <img src="<?php echo \Elementor\Utils::get_placeholder_image_src(); ?>" alt="{{ botName }}" class="modern-ai-chat__header-avatar modern-ai-chat__header-avatar--placeholder">
				<div class="modern-ai-chat__header-title">{{{ chatHeaderTitle }}}</div>
			</div>
            <div class="modern-ai-chat__messages-container" style="min-height: 150px; background: #f0f0f0; text-align:center; padding-top: 50px;">
                <?php esc_html_e( 'Chat Preview Area', 'modern-ai-chat-elementor' ); ?>
                <# if ( initialGreeting ) { #>
                    <p style="font-size: smaller; color: #555; margin-top:10px;"><i><?php esc_html_e( 'Initial Greeting:', 'modern-ai-chat-elementor' ); ?> {{{ initialGreeting }}}</i></p>
                <# } #>
            </div>
			<div class="modern-ai-chat__input-area">
				<input type="text" class="modern-ai-chat__input" placeholder="{{{ inputPlaceholder }}}" disabled>
				<button class="modern-ai-chat__send-button" aria-label="<?php esc_attr_e('Send Message', 'modern-ai-chat-elementor'); ?>" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/></svg>
				</button>
			</div>
            <div style="text-align:center; padding:10px; background:#fff; border-top:1px solid #eee; font-size:12px; color:#777;">
                <?php esc_html_e( 'Full chat functionality available on the frontend.', 'modern-ai-chat-elementor' ); ?>
            </div>
		</div>
        <?php
	}

    /**
     * Get script dependencies.
     *
     * Retrieve the list of script dependencies the widget requires.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget scripts dependencies.
     */
    public function get_script_depends() {
        return [ 'modern-ai-chat-widget-script' ];
    }

    /**
     * Get style dependencies.
     *
     * Retrieve the list of style dependencies the widget requires.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget styles dependencies.
     */
    public function get_style_depends() {
        return [ 'modern-ai-chat-widget-style' ];
    }

}
