(function($) {
    'use strict';

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
        const settings = $widget.data('settings');
        if (!settings) {
            console.error('Modern AI Chat: Widget settings not found.', $widget);
            return;
        }

        const elements = {
            messagesContainer: $widget.find('.modern-ai-chat__messages-container'),
            messages: $widget.find('.modern-ai-chat__messages'),
            input: $widget.find('.modern-ai-chat__input'),
            sendButton: $widget.find('.modern-ai-chat__send-button'),
            headerAvatar: $widget.find('.modern-ai-chat__header-avatar'), // For bot avatar in header
            botAvatarTemplate: settings.bot_avatar_url ? `<img src="${settings.bot_avatar_url}" alt="${settings.bot_name || 'Bot'}" class="modern-ai-chat__message-avatar">` : `<div class="modern-ai-chat__message-avatar modern-ai-chat__message-avatar--placeholder"></div>`
        };
        // Header avatar src is handled by the PHP render method, including placeholder.
        // No dynamic JS setting needed here for the initial header avatar.

        let sessionId = $widget.data('sessionId') || generateSessionId();
        $widget.data('sessionId', sessionId);

        let isTyping = false;

        // --- Helper Functions ---

        function getCurrentTime() {
            return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function scrollToBottom() {
            elements.messagesContainer.stop().animate({
                scrollTop: elements.messagesContainer[0].scrollHeight
            }, 300);
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
            elements.messages.append(typingIndicatorHTML);
            scrollToBottom();
        }

        function hideTypingIndicator() {
            if (!isTyping) return;
            isTyping = false;
            $widget.find('.modern-ai-chat__typing-indicator').remove();
        }

        function escapeHTML(str) {
            if (typeof str !== 'string') return str;
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
            hideTypingIndicator(); // Always hide typing before adding a new message
            const time = getCurrentTime();
            let messageHTML = '';
            const sanitizedText = escapeHTML(text); // Sanitize text before displaying

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
                            ${senderName ? `<div class="modern-ai-chat__message-name">${escapeHTML(senderName)}</div>` : ''}
                            <div class="modern-ai-chat__message-bubble">
                                <div class="modern-ai-chat__message-text">${sanitizedText}</div>
                                <div class="modern-ai-chat__message-time">${time}</div>
                            </div>
                        </div>
                    </div>`;
            }
            elements.messages.append(messageHTML);
            scrollToBottom();
        }


        function handleSendMessage() {
            const messageText = elements.input.val().trim();
            if (!messageText) return;

            addMessage(messageText, 'user');
            elements.input.val('');
            elements.sendButton.prop('disabled', true); // Disable button while sending
            showTypingIndicator();

            let webhookUrlToUse = settings.webhook_url;
            if (!webhookUrlToUse && modernAIChat.dev_webhook_url) {
                 console.warn("Modern AI Chat: Webhook URL not set in widget settings. Using development webhook.");
                 webhookUrlToUse = modernAIChat.dev_webhook_url;
            } else if (!webhookUrlToUse) {
                console.error("Modern AI Chat: Webhook URL is not configured.");
                addMessage("Error: Webhook URL is not configured. Please set it in the widget settings.", 'bot', settings.bot_name);
                hideTypingIndicator();
                elements.sendButton.prop('disabled', false);
                return;
            }

            try {
                $.ajax({
                    url: modernAIChat.ajax_url, // Global AJAX URL from wp_localize_script
                    type: 'POST',
                    data: {
                        action: 'send_chat_message',
                        nonce: modernAIChat.nonce, // Global nonce
                        message: messageText,
                        sessionId: sessionId,
                        webhook_url: webhookUrlToUse
                    },
                    success: function(response) {
                        // Defensive check for response and response.data
                        if (response && response.success && response.data !== undefined) {
                            let botResponseText = '';
                            if (typeof response.data === 'string') {
                                try {
                                    const parsedData = JSON.parse(response.data);
                                    // Common patterns for webhook responses
                                    botResponseText = parsedData.response || parsedData.message || parsedData.text || response.data;
                                } catch (e) {
                                    // If parsing fails, but it's a string, use the string.
                                    botResponseText = response.data;
                                    console.warn('Modern AI Chat: Webhook response (string) was not valid JSON.', e, response.data);
                                }
                            } else if (typeof response.data === 'object' && response.data !== null) {
                                botResponseText = response.data.response || response.data.message || response.data.text || JSON.stringify(response.data);
                            } else if (response.data) { // For boolean, number, etc.
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
                            // Handle cases where 'response' or 'response.data' is null/undefined unexpectedly
                            addMessage('Error: Received an invalid response from the server.', 'bot', settings.bot_name || 'AI Assistant');
                            console.error('Modern AI Chat: AJAX Error - Invalid response structure.', response);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        let detailedError = textStatus;
                        if (errorThrown) {
                            detailedError += `, ${errorThrown}`;
                        }
                        if (jqXHR.responseText) {
                             // Only log responseText in console, not to user, as it might be HTML/complex.
                            console.error('Modern AI Chat: AJAX Request Failed - Server Response:', jqXHR.responseText);
                        }
                        addMessage(`Error: Could not connect to the server. (${detailedError})`, 'bot', settings.bot_name || 'AI Assistant');
                        console.error('Modern AI Chat: AJAX Request Failed - ', textStatus, errorThrown);
                    },
                    complete: function() {
                        hideTypingIndicator();
                        elements.sendButton.prop('disabled', false);
                        if (elements.input && typeof elements.input.focus === 'function') {
                            elements.input.focus();
                        }
                    }
                });
            } catch (e) {
                console.error('Modern AI Chat: Synchronous error setting up AJAX request -', e);
                addMessage('Error: Could not initiate message sending. Please try again.', 'bot', settings.bot_name || 'AI Assistant');
                hideTypingIndicator();
                elements.sendButton.prop('disabled', false);
                 if (elements.input && typeof elements.input.focus === 'function') {
                    elements.input.focus();
                }
            }
        }

        // --- Event Listeners ---
        elements.sendButton.on('click', handleSendMessage);
        elements.input.on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                handleSendMessage();
            }
        });

        // --- Initialization ---
        if (settings.initial_greeting) {
            setTimeout(function() { // Add a slight delay for effect
                 addMessage(settings.initial_greeting, 'bot', settings.bot_name);
            }, 500);
        }

        // Enable send button by default (if input is not empty, it will be handled by input event)
        elements.sendButton.prop('disabled', false);
        elements.input.on('input', function() {
            elements.sendButton.prop('disabled', $(this).val().trim() === '');
        });

        console.log('Modern AI Chat widget initialized:', settings.widgetId, "Session ID:", sessionId);
    }


    $(document).ready(function() {
        // Initialize for widgets already on the page
        $('.modern-ai-chat').each(function() {
            initializeModernChatWidget($(this));
        });

        // For Elementor Editor: Re-initialize when a new widget is added or settings change
        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            window.elementorFrontend.hooks.addAction('frontend/element_ready/modern-ai-chat.default', function($scope) {
                const $widget = $scope.find('.modern-ai-chat');
                if ($widget.length && !$widget.data('initialized')) { // Check if already initialized
                    initializeModernChatWidget($widget);
                    $widget.data('initialized', true);
                }
            });
        }
    });

})(jQuery);
