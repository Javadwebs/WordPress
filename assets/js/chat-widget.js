(function($) {
    'use strict';

    // Early check for critical localized data
    if (typeof modernAIChat === 'undefined' || !modernAIChat.ajax_url || !modernAIChat.nonce) {
        console.error('Modern AI Chat: CRITICAL - Localized data (modernAIChat object with ajax_url and nonce) is missing. Chat functionality will be severely impaired or non-functional. Ensure wp_localize_script is working correctly for both editor and frontend.');
        // Depending on the desired behavior, you might want to prevent further execution
        // For now, we'll let it proceed so other console logs can provide more context if this isn't the sole issue.
    }


    /**
     * Generates a simple v4 UUID.
     * @returns {string} A unique session ID.
     */
    function generateSessionId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    /**
     * Initializes a single Modern AI Chat widget.
     * @param {jQuery} $widget The jQuery object for the widget container.
     */
    function initializeModernChatWidget($widget) {
        console.log('Modern AI Chat: Initializing widget. Widget DOM:', $widget[0]);
        try {
            const settingsJson = $widget.attr('data-settings');
            if (!settingsJson) {
                console.error('Modern AI Chat: data-settings attribute not found or empty on widget.', $widget);
                // Optionally display an error message directly in the widget placeholder
                $widget.html('<p style="color:red; padding:10px;">Error: Chat settings data missing.</p>');
                return;
            }

            let settings;
            try {
                settings = JSON.parse(settingsJson);
            } catch (e) {
                console.error('Modern AI Chat: Failed to parse settings JSON.', { json: settingsJson, error: e, widget: $widget[0] });
                $widget.html('<p style="color:red; padding:10px;">Error: Invalid chat settings format.</p>');
                return;
            }

            console.log('Modern AI Chat: Parsed Settings:', settings);

            if (!settings || typeof settings !== 'object') {
                console.error('Modern AI Chat: Widget settings are not a valid object after parsing.', { widget: $widget[0], parsedSettings: settings });
                $widget.html('<p style="color:red; padding:10px;">Error: Chat settings invalid structure.</p>');
                return;
            }

            const elements = {
                messagesContainer: $widget.find('.modern-ai-chat__messages-container'),
                messages: $widget.find('.modern-ai-chat__messages'),
                input: $widget.find('.modern-ai-chat__input'),
                sendButton: $widget.find('.modern-ai-chat__send-button'),
                headerAvatar: $widget.find('.modern-ai-chat__header-avatar'),
                botAvatarTemplate: (settings.bot_avatar_url ? `<img src="${escapeHTML(settings.bot_avatar_url)}" alt="${escapeHTML(settings.bot_name || 'Bot')}" class="modern-ai-chat__message-avatar">` : `<div class="modern-ai-chat__message-avatar modern-ai-chat__message-avatar--placeholder"></div>`)
            };

            console.log('Modern AI Chat: DOM Elements selected:', elements);
             if (!elements.messagesContainer.length) console.warn('Modern AI Chat: messagesContainer not found');
             if (!elements.messages.length) console.warn('Modern AI Chat: messages element not found');
             if (!elements.input.length) console.warn('Modern AI Chat: input element not found');
             if (!elements.sendButton.length) console.warn('Modern AI Chat: sendButton not found');


            let sessionId = $widget.data('sessionId') || generateSessionId();
            $widget.data('sessionId', sessionId);
            console.log('Modern AI Chat: Session ID:', sessionId);

            let isTyping = false;

            // --- Helper Functions ---
            console.log('Modern AI Chat: Defining helper functions...');

            function getCurrentTime() {
                return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            function scrollToBottom() {
                if (elements.messagesContainer && elements.messagesContainer.length > 0 && elements.messagesContainer[0].scrollHeight) {
                     // Check if the container is actually scrollable and visible
                    if (elements.messagesContainer[0].scrollHeight > elements.messagesContainer[0].clientHeight && $widget.is(":visible")) {
                        elements.messagesContainer.stop().animate({
                            scrollTop: elements.messagesContainer[0].scrollHeight
                        }, 300);
                    }
                } else {
                    // console.warn('Modern AI Chat: Messages container not found or not scrollable for scrollToBottom.');
                }
            }

            function showTypingIndicator() {
                if (isTyping) return;
                isTyping = true;
                const typingIndicatorHTML = `
                    <div class="modern-ai-chat__message modern-ai-chat__message--bot modern-ai-chat__typing-indicator">
                        ${elements.botAvatarTemplate}
                        <div class="modern-ai-chat__message-content">
                            <div class="modern-ai-chat__message-bubble">
                                <div class="modern-ai-chat__typing-dot"></div>
                                <div class="modern-ai-chat__typing-dot"></div>
                                <div class="modern-ai-chat__typing-dot"></div>
                            </div>
                        </div>
                    </div>`;
                if (elements.messages && elements.messages.length > 0) {
                    elements.messages.append(typingIndicatorHTML);
                    scrollToBottom();
                } else {
                     console.warn('Modern AI Chat: Messages element not found for showTypingIndicator.');
                }
            }

            function hideTypingIndicator() {
                if (!isTyping) return;
                isTyping = false;
                $widget.find('.modern-ai-chat__typing-indicator').remove();
            }

            function escapeHTML(str) {
                if (str === null || str === undefined) return '';
                if (typeof str !== 'string') return String(str);
                return str.replace(/[&<>"']/g, function (match) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    }[match];
                });
            }

            function addMessage(text, type, senderName = '') {
                console.log(`Modern AI Chat: Adding message - Type: ${type}, Text: ${(String(text)).substring(0,50)}...`);
                hideTypingIndicator();
                const time = getCurrentTime();
                let messageHTML = '';
                const textToDisplay = (text === null || text === undefined) ? '' : String(text);
                const sanitizedText = escapeHTML(textToDisplay);
                const botDisplayName = escapeHTML(settings.bot_name || 'AI Assistant');

                if (type === 'user') {
                    messageHTML = `
                        <div class="modern-ai-chat__message modern-ai-chat__message--user">
                            <div class="modern-ai-chat__message-content">
                                <div class="modern-ai-chat__message-bubble">
                                    <div class="modern-ai-chat__message-text">${sanitizedText}</div>
                                    <div class="modern-ai-chat__message-time">${time}</div>
                                </div>
                            </div>
                        </div>`;
                } else { // 'bot'
                    messageHTML = `
                        <div class="modern-ai-chat__message modern-ai-chat__message--bot">
                            ${elements.botAvatarTemplate}
                            <div class="modern-ai-chat__message-content">
                                ${senderName ? `<div class="modern-ai-chat__message-name">${escapeHTML(senderName)}</div>` : (botDisplayName ? `<div class="modern-ai-chat__message-name">${botDisplayName}</div>` : '')}
                                <div class="modern-ai-chat__message-bubble">
                                    <div class="modern-ai-chat__message-text">${sanitizedText}</div>
                                    <div class="modern-ai-chat__message-time">${time}</div>
                                </div>
                            </div>
                        </div>`;
                }

                if (elements.messages && elements.messages.length > 0) {
                    elements.messages.append(messageHTML);
                    scrollToBottom();
                } else {
                    console.error('Modern AI Chat: Messages element not found for appending message.');
                }
            }

            function handleSendMessage() {
                console.log('Modern AI Chat: handleSendMessage function invoked.'); // Log when function is called
                if (!elements.input || elements.input.length === 0) {
                    console.error('Modern AI Chat: Input element not found in handleSendMessage.');
                    return;
                }
                const messageText = elements.input.val().trim();
                if (!messageText) {
                    console.log('Modern AI Chat: Empty message, not sending.');
                    return;
                }

                addMessage(messageText, 'user');
                elements.input.val('');
                if (elements.sendButton && elements.sendButton.length > 0) {
                    elements.sendButton.prop('disabled', true);
                }
                showTypingIndicator();

                let webhookUrlToUse = settings.webhook_url;
                console.log('Modern AI Chat: Webhook URL from settings:', settings.webhook_url);
                console.log('Modern AI Chat: Development Webhook URL from localized data:', modernAIChat.dev_webhook_url);

                if (!webhookUrlToUse && modernAIChat.dev_webhook_url) {
                     console.warn("Modern AI Chat: Webhook URL not set in widget settings. Using development webhook.");
                     webhookUrlToUse = modernAIChat.dev_webhook_url;
                } else if (!webhookUrlToUse) {
                    console.error("Modern AI Chat: Webhook URL is not configured in settings and no dev URL available.");
                    addMessage("Error: Webhook URL is not configured. Please set it in the widget settings.", 'bot', settings.bot_name || 'AI Assistant');
                    hideTypingIndicator();
                    if (elements.sendButton && elements.sendButton.length > 0) {
                        elements.sendButton.prop('disabled', false);
                    }
                    return;
                }

                console.log('Modern AI Chat: Attempting to send message. Text:', messageText, 'SessionID:', sessionId, 'Webhook URL:', webhookUrlToUse);

                try {
                    $.ajax({
                        url: modernAIChat.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'send_chat_message',
                            nonce: modernAIChat.nonce,
                            message: messageText,
                            sessionId: sessionId,
                            webhook_url: webhookUrlToUse
                        },
                        success: function(response) {
                            console.log('Modern AI Chat: AJAX success response:', response);
                            if (response && response.success && response.data !== undefined) {
                                let botResponseText = '';
                                if (typeof response.data === 'string') {
                                    try {
                                        const parsedData = JSON.parse(response.data);
                                        botResponseText = parsedData.response || parsedData.message || parsedData.text || response.data;
                                    } catch (e) {
                                        botResponseText = response.data;
                                        console.warn('Modern AI Chat: Webhook response (string) was not valid JSON.', e, response.data);
                                    }
                                } else if (typeof response.data === 'object' && response.data !== null) {
                                    botResponseText = response.data.response || response.data.message || response.data.text || JSON.stringify(response.data);
                                } else if (response.data !== null && response.data !== undefined) {
                                    botResponseText = String(response.data);
                                } else {
                                    botResponseText = "Received an empty or unexpected response from the bot.";
                                    console.warn('Modern AI Chat: Webhook response data is empty or undefined type.', response);
                                }
                                addMessage(botResponseText, 'bot', settings.bot_name || 'AI Assistant');
                            } else if (response && !response.success) {
                                const errorMessage = (response.data && response.data.message) ? response.data.message : 'An error occurred with the chat service.';
                                addMessage(`Error: ${errorMessage}`, 'bot', settings.bot_name || 'AI Assistant');
                                console.error('Modern AI Chat: AJAX Error (success:false) - ', response.data);
                            } else {
                                addMessage('Error: Received an invalid response from the server.', 'bot', settings.bot_name || 'AI Assistant');
                                console.error('Modern AI Chat: AJAX Error - Invalid response structure.', response);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            let detailedError = textStatus;
                            if (errorThrown) {
                                detailedError += `, ${errorThrown}`;
                            }
                            if (jqXHR && jqXHR.responseText) {
                                console.error('Modern AI Chat: AJAX Request Failed - Server Response:', jqXHR.responseText);
                            }
                            addMessage(`Error: Could not connect to the server. (${detailedError})`, 'bot', settings.bot_name || 'AI Assistant');
                            console.error('Modern AI Chat: AJAX Request Failed - ', textStatus, errorThrown);
                        },
                        complete: function() {
                            hideTypingIndicator();
                            if (elements.sendButton && elements.sendButton.length > 0) {
                                elements.sendButton.prop('disabled', false);
                            }
                            if (elements.input && elements.input.length > 0 && typeof elements.input.focus === 'function') {
                                elements.input.focus();
                            }
                        }
                    });
                } catch (e) {
                    console.error('Modern AI Chat: Synchronous error in handleSendMessage or setting up AJAX request -', e);
                    addMessage('Error: Could not initiate message sending due to an internal error. Please try again.', 'bot', settings.bot_name || 'AI Assistant');
                    hideTypingIndicator();
                    if (elements.sendButton && elements.sendButton.length > 0) {
                        elements.sendButton.prop('disabled', false);
                    }
                    if (elements.input && elements.input.length > 0 && typeof elements.input.focus === 'function') {
                       elements.input.focus();
                    }
                }
            }

            // --- Event Listeners ---
            console.log('Modern AI Chat: Setting up event listeners...');
            if (elements.sendButton && elements.sendButton.length > 0) {
                elements.sendButton.off('click.modernAIChat').on('click.modernAIChat', function() {
                    console.log('Modern AI Chat: Send button clicked.');
                    handleSendMessage();
                });
                console.log('Modern AI Chat: Send button click listener attached.');
            } else {
                console.warn('Modern AI Chat: Send button not found, cannot attach click listener.');
            }

            if (elements.input && elements.input.length > 0) {
                elements.input.off('keypress.modernAIChat input.modernAIChat');
                elements.input.on('keypress.modernAIChat', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        console.log('Modern AI Chat: Enter key pressed in input.');
                        handleSendMessage();
                    }
                });
                elements.input.on('input.modernAIChat', function() {
                    if (elements.sendButton && elements.sendButton.length > 0) {
                        elements.sendButton.prop('disabled', $(this).val().trim() === '');
                    }
                });
                console.log('Modern AI Chat: Input field listeners attached.');
            } else {
                console.warn('Modern AI Chat: Input field not found, cannot attach listeners.');
            }

            // --- Initialization ---
            console.log('Modern AI Chat: Performing widget specific initialization...');
            if (settings.initial_greeting) {
                setTimeout(function() {
                     addMessage(settings.initial_greeting, 'bot', settings.bot_name || 'AI Assistant');
                }, 500);
            }

            if (elements.input && elements.input.length > 0 && elements.sendButton && elements.sendButton.length > 0) {
                elements.sendButton.prop('disabled', elements.input.val() ? elements.input.val().trim() === '' : true);
            }

            console.log('Modern AI Chat: Widget initialized successfully:', settings.widgetId, "Session ID:", sessionId);

        } catch (e) {
            console.error('Modern AI Chat: CRITICAL ERROR during widget initialization for', $widget[0], e);
            if ($widget && $widget.length > 0) {
                // Avoid manipulating HTML if $widget itself is problematic or not fully formed
                try {
                    $widget.html('<p style="color:red; padding:10px;">Error initializing chat widget. Check console.</p>');
                } catch (htmlError) {
                    console.error('Modern AI Chat: Error trying to display error message in widget.', htmlError);
                }
            }
        }
    }


    $(document).ready(function() {
        console.log('Modern AI Chat: Document ready. Initializing widgets on page load.');
        $('.modern-ai-chat').each(function() {
            const $currentWidget = $(this);
            if (!$currentWidget.data('modern-ai-chat-initialized')) {
                console.log('Modern AI Chat: Initializing widget on document ready:', $currentWidget.attr('id') || 'N/A');
                initializeModernChatWidget($currentWidget);
                $currentWidget.data('modern-ai-chat-initialized', true);
            } else {
                console.log('Modern AI Chat: Widget already initialized on document ready, skipping:', $currentWidget.attr('id') || 'N/A');
            }
        });

        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            console.log('Modern AI Chat: Elementor frontend detected. Setting up hooks.');
            window.elementorFrontend.hooks.addAction('frontend/element_ready/modern-ai-chat.default', function($scope) {
                console.log('Modern AI Chat: Elementor element_ready hook fired.', $scope);
                const $widget = $scope.find('.modern-ai-chat');
                if ($widget.length && !$widget.data('modern-ai-chat-initialized')) {
                    try {
                        console.log('Modern AI Chat: Initializing widget from Elementor hook.', $widget.attr('id') || 'N/A');
                        initializeModernChatWidget($widget);
                        $widget.data('modern-ai-chat-initialized', true);
                    } catch (e) {
                        console.error('Modern AI Chat: Error initializing widget from Elementor hook.', e, $scope);
                    }
                } else if ($widget.length && $widget.data('modern-ai-chat-initialized')) {
                    console.log('Modern AI Chat: Widget already initialized (Elementor hook), skipping:', $widget.attr('id') || 'N/A');
                } else if (!$widget.length) {
                    console.warn('Modern AI Chat: Widget element .modern-ai-chat not found in $scope for Elementor hook.', $scope);
                }
            });
        } else {
            console.log('Modern AI Chat: Elementor frontend not detected or hooks not available.');
        }
    });

})(jQuery);
